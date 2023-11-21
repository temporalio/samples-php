<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors\Activity;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;
use Temporal\Samples\Interceptors\Attribute\StartToCloseTimeout;

#[StartToCloseTimeout(5)]
#[ActivityInterface(prefix: 'Interceptors.')]
class Sleeper
{
    #[ActivityMethod]
    public function sleep(int $howToSleep)
    {
        \sleep($howToSleep);
        return 'I am awake!';
    }

    #[StartToCloseTimeout(2)]
    #[ActivityMethod]
    public function sleep2(int $howToSleep)
    {
        \sleep($howToSleep);
        return 'I am awake!';
    }
}
