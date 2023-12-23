<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\SampleUtils;

use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\ScheduleClient;
use Temporal\Client\ScheduleClientInterface;
use Temporal\Client\WorkflowClient;
use Temporal\Client\WorkflowClientInterface;
use Temporal\Interceptor\SimplePipelineProvider;
use Temporal\OpenTelemetry\Interceptor\OpenTelemetryWorkflowClientCallsInterceptor;

class Command extends \Symfony\Component\Console\Command\Command
{
    // Command name.
    protected const NAME = '';

    //  Short command description.
    protected const DESCRIPTION = '';

    // Command options specified in Symfony format. For more complex definitions redefine
    // getOptions() method.
    protected const OPTIONS = [];

    // Command arguments specified in Symfony format. For more complex definitions redefine
    // getArguments() method.
    protected const ARGUMENTS = [];

    protected WorkflowClientInterface $workflowClient;
    protected ScheduleClientInterface $scheduleClient;

    /**
     * Command constructor.
     */
    public function __construct(ServiceClient $serviceClient)
    {
        parent::__construct();

        $this->workflowClient = WorkflowClient::create(
            serviceClient: $serviceClient,
            interceptorProvider: new SimplePipelineProvider([
                new OpenTelemetryWorkflowClientCallsInterceptor(TracerFactory::create('interceptors-sample-client')),
            ])
        );
        $this->scheduleClient = ScheduleClient::create($serviceClient);
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

    /**
     * @param string $class
     * @return static
     */
    public static function create(string $class, ServiceClient $client): self
    {
        return new $class($client);
    }
}