<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Exception;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface]
class FailedActivity
{
    public function fail(string $greeting, string $name)
    {
        throw new \Error($greeting . ' ' . $name . '!');
    }
}