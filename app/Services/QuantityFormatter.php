<?php

namespace App\Services;

use App\Models\UnitOfMeasurement;

class QuantityFormatter
{
    /**
     * Format a stock quantity cleanly:
     * - Whole numbers: formatted without decimals (e.g. 14, 18, 10, 1,000)
     * - Fractional numbers: preserves full decimal precision without trailing zeros (e.g. 2.5, 1.25, 0.75, 1,250.5)
     * - Optional UOM appending (e.g. "14 BTL", "2.5 LTR")
     */
    public static function format(mixed $value, ?string $uom = null, bool $withThousandsSeparator = true): string
    {
        if ($value === null || $value === '') {
            $formatted = '0';
        } else {
            $floatVal = (float) $value;

            // Check if value is a whole number (integer)
            if (floor($floatVal) == $floatVal) {
                $formatted = $withThousandsSeparator
                    ? number_format($floatVal, 0)
                    : (string) ((int) $floatVal);
            } else {
                // Preserve fractional precision up to 4 decimal places without trailing zeros
                $formattedWithDecimals = $withThousandsSeparator
                    ? number_format($floatVal, 4)
                    : sprintf('%.4f', $floatVal);

                $formatted = rtrim(rtrim($formattedWithDecimals, '0'), '.');
            }
        }

        if ($uom !== null && trim($uom) !== '') {
            return $formatted.' '.trim($uom);
        }

        return $formatted;
    }

    /**
     * Check if a given UOM allows fractional quantities or requires whole numbers.
     * Discrete units like Item, Packet, Piece, Unit, Box require whole numbers.
     * Continuous units like Litre, Kilogram, Loose, etc. allow fractional.
     */
    public static function allowsFractional(?UnitOfMeasurement $uom): bool
    {
        if (! $uom) {
            return true;
        }

        $name = strtolower(trim($uom->name ?? ''));
        $code = strtolower(trim($uom->code ?? ''));

        $discreteNames = ['item', 'packet', 'piece', 'pcs', 'unit', 'box', 'set', 'pair', 'pack'];
        $discreteCodes = ['itm', 'pkt', 'pcs', 'unt', 'box', 'set', 'pr'];

        if (in_array($name, $discreteNames, true) || in_array($code, $discreteCodes, true)) {
            return false;
        }

        return true;
    }

    /**
     * Validate whether a given quantity is valid for the specified UOM.
     */
    public static function isValidForUom(mixed $quantity, ?UnitOfMeasurement $uom): bool
    {
        if (! is_numeric($quantity)) {
            return false;
        }

        $floatQty = (float) $quantity;
        if ($floatQty < 0) {
            return false;
        }

        if (! self::allowsFractional($uom)) {
            return floor($floatQty) == $floatQty;
        }

        return true;
    }
}
