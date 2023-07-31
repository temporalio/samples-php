<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleActivityException;

use Temporal\Activity;

// @@@SNIPSTART php-hello-activity
class GreetingActivity implements GreetingActivityInterface
{
    public function composeGreeting(string $greeting, string $name): string
    {
        echo 'attempt: ' . Activity::getInfo()->attempt;

        throw new \Exception("not yet" . Activity::getInfo()->attempt);
    }
}
// @@@SNIPEND
