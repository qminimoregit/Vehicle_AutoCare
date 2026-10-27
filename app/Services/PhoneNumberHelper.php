<?php

namespace App\Services;

class PhoneNumberHelper
{
    /**
     * Normalize a phone number into WhatsApp international format (e.g. 94712345678).
     * Supports standard Sri Lankan phone formats (07XXXXXXXX, +947XXXXXXXX, etc.).
     */
    public static function normalizeForWhatsApp(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Remove whitespace, dashes, parentheses
        $clean = preg_replace('/[\s\-\(\)\.]/', '', trim($phone));

        if (empty($clean)) {
            return null;
        }

        // Handle leading +
        if (str_starts_with($clean, '+')) {
            $clean = substr($clean, 1);
        }

        // Handle 0094...
        if (str_starts_with($clean, '00')) {
            $clean = substr($clean, 2);
        }

        // Standard Sri Lankan local mobile format: 07XXXXXXXX (10 digits)
        if (preg_match('/^0([1-9][0-9]{8})$/', $clean, $matches)) {
            return '94'.$matches[1];
        }

        // Local 9-digit format without leading 0: 7XXXXXXXX
        if (preg_match('/^([1-9][0-9]{8})$/', $clean, $matches)) {
            return '94'.$matches[1];
        }

        // Already with 94 prefix (947XXXXXXXX)
        if (preg_match('/^94[0-9]{9}$/', $clean)) {
            return $clean;
        }

        // If it's other international format, just digits
        $digitsOnly = preg_replace('/\D/', '', $clean);

        return $digitsOnly ?: null;
    }

    /**
     * Generate prefilled wa.me URL for manual messaging.
     */
    public static function buildWhatsAppUrl(?string $phone, string $message): ?string
    {
        $normalized = self::normalizeForWhatsApp($phone);
        if (! $normalized) {
            return null;
        }

        return 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }
}
