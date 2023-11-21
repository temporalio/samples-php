<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\Interceptors\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class StartToCloseTimeout implements ActivityOption
{
    public function __construct(
        public readonly string|int|float|\DateInterval $timeout,
    ) {
    }
}
