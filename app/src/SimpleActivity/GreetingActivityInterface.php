<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleActivity;

// @@@SNIPSTART php-hello-activity-interface
use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'SimpleActivity.')]
interface GreetingActivityInterface
{
    #[ActivityMethod(name: "ComposeGreeting")]
    public function composeGreeting(
        string $greeting,
        string $name
    ): string;
}
// @@@SNIPEND
