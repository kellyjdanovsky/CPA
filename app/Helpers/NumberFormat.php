<?php

namespace App\Helpers;

class NumberFormat
{
    /**
     * Format a number with exactly 2 decimal places without rounding
     * 
     * @param float $number The number to format
     * @param int $decimals Number of decimal places (default: 2)
     * @return string Formatted number as string
     */
    public static function formatWithoutRounding($number, $decimals = 2)
    {
        // Convert to string to avoid floating point precision issues
        $numberStr = (string) $number;
        
        // If it's an integer, add decimal places
        if (strpos($numberStr, '.') === false) {
            $numberStr .= '.';
            for ($i = 0; $i < $decimals; $i++) {
                $numberStr .= '0';
            }
            return $numberStr;
        }
        
        // Split the number into integer and decimal parts
        $parts = explode('.', $numberStr);
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? $parts[1] : '';
        
        // Truncate decimal part to the specified number of decimals
        if (strlen($decimalPart) > $decimals) {
            $decimalPart = substr($decimalPart, 0, $decimals);
        } else {
            // Pad with zeros if needed
            while (strlen($decimalPart) < $decimals) {
                $decimalPart .= '0';
            }
        }
        
        // Return formatted number
        return $integerPart . '.' . $decimalPart;
    }
}