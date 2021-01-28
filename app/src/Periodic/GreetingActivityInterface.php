<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Periodic;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface(prefix: 'Periodic.')]
interface GreetingActivityInterface
{
    #[ActivityMethod(name: "Greet")]
    public function greet(
        string $greeting
    ): string;
}