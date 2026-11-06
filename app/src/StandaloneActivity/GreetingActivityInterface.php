<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\StandaloneActivity;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'StandaloneActivity.')]
interface GreetingActivityInterface
{
    public const TYPE = 'StandaloneActivity.ComposeGreeting';

    #[ActivityMethod(name: 'ComposeGreeting')]
    public function composeGreeting(string $name): string;
}
