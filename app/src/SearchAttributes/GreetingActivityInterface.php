<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SearchAttributes;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
interface GreetingActivityInterface
{
    #[ActivityMethod]
    public function composeGreeting(
        string $greeting,
        string $name
    ): string;
}