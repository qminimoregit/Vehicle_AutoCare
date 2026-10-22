<?php

namespace App\Helpers;

use Carbon\Carbon;
use DateTimeInterface;

class TimeHelper
{
    public const TIMEZONE = 'Asia/Colombo';

    /**
     * Get current Sri Lanka local time as a Carbon instance.
     */
    public static function now(): Carbon
    {
        return Carbon::now(config('app.display_timezone', self::TIMEZONE));
    }

    /**
     * Get today's start of day in Sri Lanka local time.
     */
    public static function today(): Carbon
    {
        return Carbon::today(config('app.display_timezone', self::TIMEZONE));
    }

    /**
     * Convert a datetime string or DateTimeInterface object into Sri Lanka local time.
     * Preserves UTC storage convention by treating database timestamps as UTC before converting.
     */
    public static function toSriLanka(DateTimeInterface|string|null $datetime): ?Carbon
    {
        if (! $datetime) {
            return null;
        }

        $targetTimezone = config('app.display_timezone', self::TIMEZONE);

        if (is_string($datetime)) {
            // DB timestamps are stored in UTC
            $datetime = Carbon::parse($datetime, config('app.timezone', 'UTC'));
        } elseif ($datetime instanceof Carbon) {
            $datetime = $datetime->copy();
        } else {
            $datetime = Carbon::instance($datetime);
        }

        return $datetime->setTimezone($targetTimezone);
    }

    /**
     * Format a datetime value into Sri Lanka local time string.
     */
    public static function format(DateTimeInterface|string|null $datetime, string $format = 'd/m/Y h:i A'): string
    {
        $converted = self::toSriLanka($datetime);

        return $converted ? $converted->format($format) : '';
    }

    /**
     * Format only the date part in Sri Lanka local time.
     */
    public static function formatDate(DateTimeInterface|string|null $datetime, string $format = 'd/m/Y'): string
    {
        return self::format($datetime, $format);
    }

    /**
     * Format only the time part in Sri Lanka local time.
     */
    public static function formatTime(DateTimeInterface|string|null $datetime, string $format = 'h:i A'): string
    {
        return self::format($datetime, $format);
    }
}
