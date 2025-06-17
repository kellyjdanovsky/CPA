<?php

namespace App\Helpers;

/**
 * Classe utilitaire pour convertir les nombres en lettres
 */
class NumberToWords
{
    private static $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
    private static $tens = ['', 'dix', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];

    /**
     * Convertit un nombre en lettres (en français)
     *
     * @param float $number Le nombre à convertir
     * @param string $currency La devise (par défaut: 'Ariary')
     * @return string Le nombre en lettres
     */
    public static function convert($number, $currency = 'Ariary')
    {
        // Arrondir le nombre pour éviter les problèmes de précision
        $number = round($number);
        
        if ($number == 0) {
            return 'zéro ' . $currency;
        }
        
        $words = '';
        
        // Traiter les milliards
        if ($number >= 1000000000) {
            $billions = floor($number / 1000000000);
            $words .= self::convertLessThanOneThousand($billions) . ' milliard' . ($billions > 1 ? 's' : '') . ' ';
            $number %= 1000000000;
        }
        
        // Traiter les millions
        if ($number >= 1000000) {
            $millions = floor($number / 1000000);
            $words .= self::convertLessThanOneThousand($millions) . ' million' . ($millions > 1 ? 's' : '') . ' ';
            $number %= 1000000;
        }
        
        // Traiter les milliers
        if ($number >= 1000) {
            $thousands = floor($number / 1000);
            $words .= ($thousands == 1 ? 'mille ' : self::convertLessThanOneThousand($thousands) . ' mille ');
            $number %= 1000;
        }
        
        // Traiter les centaines, dizaines et unités
        if ($number > 0) {
            $words .= self::convertLessThanOneThousand($number);
        }
        
        return trim($words) . ' ' . $currency;
    }
    
    /**
     * Convertit un nombre inférieur à 1000 en lettres
     *
     * @param int $number Le nombre à convertir
     * @return string Le nombre en lettres
     */
    private static function convertLessThanOneThousand($number)
    {
        $words = '';
        
        // Traiter les centaines
        if ($number >= 100) {
            $hundreds = floor($number / 100);
            $words .= ($hundreds == 1 ? 'cent ' : self::$units[$hundreds] . ' cent ');
            $number %= 100;
        }
        
        // Traiter les dizaines et unités
        if ($number > 0) {
            if ($number < 20) {
                // Nombres de 1 à 19
                $words .= self::$units[$number];
            } else {
                // Nombres de 20 à 99
                $ten = floor($number / 10);
                $unit = $number % 10;
                
                if ($ten == 7 || $ten == 9) {
                    // Cas particuliers: 70-79 et 90-99
                    $words .= self::$tens[$ten - 1] . '-';
                    $unit += 10;
                } else {
                    $words .= self::$tens[$ten];
                    if ($unit > 0) {
                        $words .= ($ten == 8 ? '-' : '-');
                    }
                }
                
                if ($unit > 0) {
                    $words .= self::$units[$unit];
                }
            }
        }
        
        return $words;
    }
}