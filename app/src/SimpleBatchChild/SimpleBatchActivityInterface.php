<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\SimpleBatchChild;

use Temporal\Activity\ActivityInterface;

#[ActivityInterface(prefix: "SimpleBatchChild.")]
interface SimpleBatchActivityInterface
{
    /**
     * @param int $batchId
     * @param array $options
     *
     * @return array
     */
    public function getBatchItemIds(int $batchId, array $options): array;

    /**
     * @param int $itemId
     * @param int $batchId
     *
     * @return int
     */
    public function processItem(int $itemId, int $batchId): int;

    /**
     * @param int $batchId
     * @param array $options
     *
     * @return void
     */
    public function batchProcessingStarted(int $batchId, array $options): void;

    /**
     * @param int $batchId
     * @param array $results
     *
     * @return void
     */
    public function batchProcessingEnded(int $batchId, array $results): void;

    /**
     * @param int $itemId
     * @param int $batchId
     *
     * @return void
     */
    public function itemProcessingStarted(int $itemId, int $batchId): void;

    /**
     * @param int $itemId
     * @param int $batchId
     *
     * @return void
     */
    public function itemProcessingEnded(int $itemId, int $batchId): void;
}
