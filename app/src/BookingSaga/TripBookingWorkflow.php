<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\BookingSaga;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;

class TripBookingWorkflow implements TripBookingWorkflowInterface
{
    /** @var \Temporal\Internal\Workflow\ActivityProxy|TripBookingActivitiesInterface */
    private $activities;

    public function __construct()
    {
        $this->activities = Workflow::newActivityStub(
            TripBookingActivitiesInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::hour(1))
                // disable retries for example to run faster
                ->withRetryOptions(RetryOptions::new()->withMaximumAttempts(1))
        );
    }

    public function bookTrip(string $name)
    {
        $saga = new Workflow\Saga();

        // Configure SAGA to run compensation activities in parallel
        $saga->setParallelCompensation(true);

        try {
            $carReservationID = yield $this->activities->reserveCar($name);
            $saga->addCompensation(fn() => yield $this->activities->cancelCar($carReservationID, $name));

            $hotelReservationID = yield $this->activities->bookHotel($name);
            $saga->addCompensation(fn() => yield $this->activities->cancelHotel($hotelReservationID, $name));

            $flightReservationID = yield $this->activities->bookFlight($name);
            $saga->addCompensation(fn() => yield $this->activities->cancelFlight($flightReservationID, $name));

            return [
                'car' => $carReservationID,
                'hotel' => $hotelReservationID,
                'flight' => $flightReservationID
            ];
        } catch (\Throwable $e) {
            yield $saga->compensate();
            throw $e;
        }
    }
}