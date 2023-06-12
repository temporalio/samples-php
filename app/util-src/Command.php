<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\SampleUtils;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\DataConverter\DataConverter;
use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\OpenTelemetry\OpenTelemetryActivityInboundInterceptor;
use Temporal\OpenTelemetry\OpenTelemetryWorkflowClientCallsInterceptor;
use Temporal\OpenTelemetry\OpenTelemetryWorkflowOutboundRequestInterceptor;

class Command extends \Symfony\Component\Console\Command\Command
{
    // Command name.
    protected const NAME = '';

    //  Short command description.
    protected const DESCRIPTION = '';

    // Command options specified in Symfony format. For more complex definitions redefine
    // getOptions() method.
    protected const OPTIONS = [
        [
            'telemetry',
            null,
            InputOption::VALUE_NONE,
            'Run with OpenTelemetry interceptors'
        ]
    ];

    // Command arguments specified in Symfony format. For more complex definitions redefine
    // getArguments() method.
    protected const ARGUMENTS = [];

    private ServiceClient $serviceClient;

    protected WorkflowClientInterface $workflowClient;

    /**
     * Command constructor.
     */
    public function __construct(ServiceClient $serviceClient)
    {
        parent::__construct();
        $this->serviceClient = $serviceClient;
    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this->setName(static::NAME);
        $this->setDescription(static::DESCRIPTION);

        foreach ($this->defineOptions() as $option) {
            call_user_func_array([$this, 'addOption'], $option);
        }

        foreach ($this->defineArguments() as $argument) {
            call_user_func_array([$this, 'addArgument'], $argument);
        }
    }

    /**
     * Define command options.
     *
     * @return array
     */
    protected function defineOptions(): array
    {
        return static::OPTIONS;
    }

    /**
     * Define command arguments.
     *
     * @return array
     */
    protected function defineArguments(): array
    {
        return static::ARGUMENTS;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $interceptors = [];
        if ($input->getOption('telemetry')) {
            $converter = DataConverter::createDefault();
            $tracer = TracerFactory::create();

            $interceptors = [
                new OpenTelemetryActivityInboundInterceptor($tracer, $converter),
                new OpenTelemetryWorkflowClientCallsInterceptor($tracer, $converter),
                new OpenTelemetryWorkflowOutboundRequestInterceptor($tracer, $converter)
            ];
        }

        $this->workflowClient = WorkflowClient::create(
            serviceClient: $this->serviceClient,
            interceptorProvider: new SimplePipelineProvider($interceptors)
        );
    }

    /**
     * @param string $class
     * @return static
     */
    public static function create(string $class, ServiceClient $client): self
    {
        return new $class($client);
    }
}