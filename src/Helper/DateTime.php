<?php

namespace Feierstoff\ToolboxBundle\Helper;

use Feierstoff\ToolboxBundle\Exception\InternalServerException;

/**
 * Helper to create DateTime(Immutable) objects from static functions
 * that automatically set the correct timezone.
 */
class DateTime {

    /**
     * @param string|null $value
     * @param string|null $timezone
     * @return \DateTimeImmutable
     * @throws InternalServerException
     */
    public static function createImmutable(?string $value = "now", ?string $timezone = "Europe/Berlin"): \DateTimeImmutable {
        try {
            return new \DateTimeImmutable($value, new \DateTimeZone($timezone));
        } catch (\Exception) {
            throw new InternalServerException();
        }
    }

    /**
     * @param string|null $value
     * @param string|null $timezone
     * @return \DateTime
     * @throws InternalServerException
     */
    public static function create(?string $value = "now", ?string $timezone = "Europe/Berlin"): \DateTime {
        try {
            return new \DateTime($value, new \DateTimeZone($timezone));
        } catch (\Exception) {
            throw new InternalServerException();
        }
    }

}