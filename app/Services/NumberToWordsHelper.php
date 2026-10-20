<?php

namespace App\Services;

class NumberToWordsHelper
{
    /**
     * Words for single numbers and teens in lowercase.
     *
     * @var array<int, string>
     */
    private static array $ones = [
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
    ];

    /**
     * Words for tens in lowercase.
     *
     * @var array<int, string>
     */
    private static array $tens = [
        2 => 'twenty',
        3 => 'thirty',
        4 => 'forty',
        5 => 'fifty',
        6 => 'sixty',
        7 => 'seventy',
        8 => 'eighty',
        9 => 'ninety',
    ];

    /**
     * Convert an amount into words.
     *
     * For LKR / Sri Lankan Rupee (and default):
     * Generates natural accounting English format, e.g.:
     * "Thirty-three thousand five hundred twelve rupees only."
     * "Twenty-three thousand forty-five rupees and thirty-four cents only."
     */
    public static function spell(float|int|string $number, ?string $currency = 'LKR'): string
    {
        $cleanNumber = is_string($number) ? str_replace(',', '', trim($number)) : $number;
        $amount = (float) $cleanNumber;
        $curr = $currency !== null ? strtoupper(trim($currency)) : 'LKR';

        return self::spellRupees($amount);
    }

    /**
     * Spell amount in natural accounting English rupees and cents.
     */
    private static function spellRupees(float $amount): string
    {
        $isNegative = $amount < 0;
        $absoluteAmount = abs($amount);

        // Safe decimal handling to avoid floating-point inaccuracies
        $formatted = number_format($absoluteAmount, 2, '.', '');
        [$wholeStr, $fractionStr] = explode('.', $formatted);

        $whole = (int) $wholeStr;
        $fraction = (int) $fractionStr;

        $wholeWords = self::convertIntegerToWords($whole);
        $currencyUnit = ($whole === 1) ? 'rupee' : 'rupees';

        if ($fraction > 0) {
            $fractionWords = ($fraction === 1)
                ? 'one cent'
                : self::convertThreeDigits($fraction).' cents';

            $result = $wholeWords.' '.$currencyUnit.' and '.$fractionWords.' only.';
        } else {
            $result = $wholeWords.' '.$currencyUnit.' only.';
        }

        if ($isNegative) {
            $result = 'negative '.$result;
        }

        return ucfirst(trim($result));
    }

    /**
     * Convert an integer into words (lowercase) using standard English scales.
     */
    private static function convertIntegerToWords(int $number): string
    {
        if ($number === 0) {
            return 'zero';
        }

        $scales = [
            1000000000000 => 'trillion',
            1000000000 => 'billion',
            1000000 => 'million',
            1000 => 'thousand',
        ];

        $parts = [];

        foreach ($scales as $scale => $name) {
            if ($number >= $scale) {
                $count = (int) floor($number / $scale);
                $number %= $scale;
                $parts[] = self::convertThreeDigits($count).' '.$name;
            }
        }

        if ($number > 0) {
            $parts[] = self::convertThreeDigits($number);
        }

        return implode(' ', $parts);
    }

    /**
     * Convert a number from 1 to 999 into words with hyphens for compound numbers 21-99.
     */
    private static function convertThreeDigits(int $n): string
    {
        $parts = [];

        if ($n >= 100) {
            $hundreds = (int) floor($n / 100);
            $parts[] = self::$ones[$hundreds].' hundred';
            $n %= 100;
        }

        if ($n > 0) {
            if ($n < 20) {
                $parts[] = self::$ones[$n];
            } else {
                $ten = (int) floor($n / 10);
                $unit = $n % 10;
                $parts[] = $unit > 0
                    ? self::$tens[$ten].'-'.self::$ones[$unit]
                    : self::$tens[$ten];
            }
        }

        return implode(' ', $parts);
    }
}
