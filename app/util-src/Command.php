<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\SampleUtils;

use Temporal\Client\WorkflowClientInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    // Command name.
    protected const NAME = '';

    //  Short command description.
    protected const DESCRIPTION = '';

    // Command options specified in Symphony format. For more complex definitions redefine
    // getOptions() method.
    protected const OPTIONS = [];

    // Command arguments specified in Symphony format. For more complex definitions redefine
    // getArguments() method.
    protected const ARGUMENTS = [];

    /**
     * @var WorkflowClientInterface
     */
    protected WorkflowClientInterface $workflowClient;

    /**
     * Command constructor.
     * @param WorkflowClientInterface $workflowClient
     */
    public function __construct(WorkflowClientInterface $workflowClient)
    {
        parent::__construct();
        $this->workflowClient = $workflowClient;
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
     * @param WorkflowClientInterface $workflowClient
     * @return static
     */
    public static function create(string $class, WorkflowClientInterface $workflowClient): self
    {
        return new $class($workflowClient);
    }
}