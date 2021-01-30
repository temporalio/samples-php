<?php

/**
 * This file is part of Temporal package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Temporal\Samples\BookingSaga;

use Temporal\Common\Uuid;
use Temporal\Exception\Failure\ActivityFailure;
use Temporal\Exception\Failure\ApplicationFailure;

class TripBookingActivity implements TripBookingActivitiesInterface
{
    public function reserveCar(string $name): string
    {
        $this->log('reserve car for "%s"', $name);

        return Uuid::v4();
    }

    public function bookFlight(string $name): string
    {
        // uncommenting this line will trigger the saga compensation
        //throw new ApplicationFailure('booking failed', 'BookingFailure', true);

        $this->log('book flight for "%s"', $name);

        return Uuid::v4();
    }

    public function bookHotel(string $name): string
    {
        $this->log('book hotel for "%s"', $name);

        return Uuid::v4();
    }

    public function cancelFlight(string $reservationID, string $name): string
    {
        $this->log('cancel flight reservation "%s" for "%s"', $reservationID, $name);

        return Uuid::v4();
    }

    public function cancelHotel(string $reservationID, string $name): string
    {
        $this->log('cancel hotel reservation "%s" for "%s"', $reservationID, $name);

        return Uuid::v4();
    }

    public function cancelCar(string $reservationID, string $name): string
    {
        $this->log('cancel car reservation "%s" for "%s"', $reservationID, $name);

        return Uuid::v4();
    }

    /**
     * @param string $message
     * @param mixed ...$arg
     */
    private function log(string $message, ...$arg)
    {
        // by default all error logs are forwarded to the application server log and docker log
        file_put_contents('php://stderr', sprintf($message, ...$arg));
    }
}