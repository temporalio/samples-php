<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Saga;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface(prefix: "Saga.")]
interface SampleActivityInterface
{
    public function execute(int $amount);

    public function compensate(int $amount);
}