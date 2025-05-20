<?php

namespace App\Constants;

use DateTimeInterface;

/**
 * Defines common formatting constants used throughout the application.
 *
 * This abstract class provides standardized date and time formats to ensure consistency.
 */
abstract class Format
{
    /**
     * Format for displaying dates (e.g., 20/05/2025).
     */
    public const DATE = 'd/m/Y';

    /**
     * Format for displaying time (e.g., 14:30:00).
     */
    public const SCHEDULE = 'H:i:s';

    /**
     * Format for storing or displaying date and time (e.g., 2025-05-20 14:30:00).
     */
    public const DATE_TIME = 'Y-m-d H:i:s';
}
