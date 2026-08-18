<?php

declare(strict_types=1);

use App\Tests\Feature\Nexus\CallerWorkflowMockTest;
use App\Tests\Feature\Nexus\CallerWorkflowTest;
use App\Tests\Feature\Nexus\CancellationTest;
use App\Tests\Feature\Nexus\ContextPropagationTest;
use App\Tests\Feature\Nexus\ManualOperationStartFailureTest;
use App\Tests\Feature\Nexus\ManualOperationTest;
use App\Tests\Feature\Nexus\Mock\FailingJobClient;
use App\Tests\Feature\Nexus\Mock\HeaderEchoNexusServiceImpl;
use App\Tests\Feature\Nexus\Mock\MockEchoClient;
use App\Tests\Feature\Nexus\Mock\MockHelloHandlerWorkflowImpl;
use App\Tests\Feature\Nexus\Mock\MockSampleNexusServiceImpl;
use App\Tests\Feature\Nexus\MultipleArgumentsTest;
use App\Tests\Feature\Nexus\NexusServiceMockTest;
use App\Tests\Feature\Nexus\Workflow\TestCancellationCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestContextEchoCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestContextHelloCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestEchoCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestHelloCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestManualJobCallerWorkflowImpl;
use App\Tests\Feature\Nexus\Workflow\TestMultiArgsHelloCallerWorkflowImpl;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\Samples\Nexus\Handler\HelloHandlerWorkflowImpl;
use Temporal\Samples\Nexus\Handler\SampleNexusServiceImpl;
use Temporal\Samples\NexusContextPropagation\Propagation\NexusOutboundContextInterceptor;
use Temporal\Samples\NexusContextPropagation\Propagation\NexusStartContextInterceptor;
use Temporal\Samples\NexusContextPropagation\Propagation\WorkflowInboundContextInterceptor;
use Temporal\Testing\WorkerFactory;

ini_set('display_errors', 'stderr');

chdir(__DIR__ . '/../..');
require_once 'vendor/autoload.php';

// Async Nexus operations (WorkflowRunOperation) start a backing workflow, so
// the worker needs a WorkflowClient threaded through to the operation context.
$workflowClient = WorkflowClient::create(
    ServiceClient::create(\getenv('TEMPORAL_ADDRESS') ?: 'localhost:7236'),
    interceptorProvider: new SimplePipelineProvider([new NexusStartContextInterceptor()]),
);

$workerFactory = WorkerFactory::create(client: $workflowClient);

// Existing — SimpleActivity feature test.
$workerFactory->newWorker(taskQueue: 'tests')
    ->registerWorkflowTypes(\Temporal\Samples\SimpleActivity\GreetingWorkflow::class)
    ->registerActivity(
        \Temporal\Samples\SimpleActivity\GreetingActivity::class,
        fn() => new \Temporal\Samples\SimpleActivity\GreetingActivity(),
    );

// === Nexus Feature tests ===
//
// One task queue per scenario, all registered together in this single rr
// process. Each test creates its own Nexus endpoint (via gRPC OperatorService
// in setUp) targeting the matching queue, runs the test caller workflow on
// the same queue, then drops the endpoint in tearDown.

// Scenario 1: real production handler. Mirrors `php app/app.php nexus`.
$workerFactory->newWorker(taskQueue: CallerWorkflowTest::TASK_QUEUE)
    ->registerWorkflowTypes(
        TestEchoCallerWorkflowImpl::class,
        TestHelloCallerWorkflowImpl::class,
        HelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new SampleNexusServiceImpl());

// Scenario 2: production service-impl with the EchoClient dependency mocked,
// and a stand-in handler workflow class that returns canned data. Demonstrates
// the DI-mock + workflow-replacement test style.
$workerFactory->newWorker(taskQueue: CallerWorkflowMockTest::TASK_QUEUE)
    ->registerWorkflowTypes(
        TestEchoCallerWorkflowImpl::class,
        TestHelloCallerWorkflowImpl::class,
        MockHelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new SampleNexusServiceImpl(new MockEchoClient()));

// Scenario 3: the entire Nexus service implementation is replaced. Useful
// when the production impl isn't reachable from the test (e.g. it lives in
// another package). The mock service still routes async ops through a real
// workflow start so the wire-level state machine is exercised end-to-end.
$workerFactory->newWorker(taskQueue: NexusServiceMockTest::TASK_QUEUE)
    ->registerWorkflowTypes(
        TestEchoCallerWorkflowImpl::class,
        TestHelloCallerWorkflowImpl::class,
        MockHelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new MockSampleNexusServiceImpl());

// Scenario 4: the NexusCancellation sample — fan-out callers + a handler
// workflow that sleeps and honours cancellation.
$workerFactory->newWorker(taskQueue: CancellationTest::TASK_QUEUE)
    ->registerWorkflowTypes(
        TestCancellationCallerWorkflowImpl::class,
        \Temporal\Samples\NexusCancellation\Handler\HelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new \Temporal\Samples\Nexus\Handler\SampleNexusServiceImpl());

// Scenario 5: the NexusContextPropagation sample — the production outbound
// interceptor on the caller side, a header-echoing service double on the
// handler side so the test can observe the propagated value.
$workerFactory->newWorker(
    taskQueue: ContextPropagationTest::TASK_QUEUE,
    interceptorProvider: new SimplePipelineProvider([
        new NexusOutboundContextInterceptor(),
        new WorkflowInboundContextInterceptor(),
    ]),
)
    ->registerWorkflowTypes(
        TestContextEchoCallerWorkflowImpl::class,
        TestContextHelloCallerWorkflowImpl::class,
        \Temporal\Samples\NexusContextPropagation\Handler\HelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new HeaderEchoNexusServiceImpl());

// Scenario 6: the NexusMultipleArguments sample — single-DTO contract backed
// by a multi-argument handler workflow.
$workerFactory->newWorker(taskQueue: MultipleArgumentsTest::TASK_QUEUE)
    ->registerWorkflowTypes(
        TestMultiArgsHelloCallerWorkflowImpl::class,
        \Temporal\Samples\NexusMultipleArguments\Handler\HelloHandlerWorkflowImpl::class,
    )
    ->registerNexusServiceImplementation(new \Temporal\Samples\NexusMultipleArguments\Handler\SampleNexusServiceImpl());

// Scenario 7: the NexusManualOperation sample — a manual handler object that
// owns start (sync fast-path or async with its own token) and cancel.
$workerFactory->newWorker(taskQueue: ManualOperationTest::TASK_QUEUE)
    ->registerWorkflowTypes(TestManualJobCallerWorkflowImpl::class)
    ->registerNexusServiceImplementation(new \Temporal\Samples\NexusManualOperation\Handler\SampleNexusService());

// Scenario 8: same manual-operation caller, but the external job backend
// rejects the async submit so the caller must surface the start failure.
$workerFactory->newWorker(taskQueue: ManualOperationStartFailureTest::TASK_QUEUE)
    ->registerWorkflowTypes(TestManualJobCallerWorkflowImpl::class)
    ->registerNexusServiceImplementation(
        new \Temporal\Samples\NexusManualOperation\Handler\SampleNexusService(new FailingJobClient()),
    );

$workerFactory->run();
