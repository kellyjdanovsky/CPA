<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Formate une date en français avec Carbon
     *
     * @param string|Carbon $date
     * @param string $format
     * @return string
     */
    public static function formatFrench($date, $format = 'DD/MM/YYYY')
    {
        try {
            if (!$date) {
                return 'N/A';
            }

            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            // Convertir au fuseau horaire UTC+3 (Riyadh) pour tous les formats
            return $carbon->setTimezone('Asia/Riyadh')->locale('fr')->isoFormat($format);
        } catch (\Exception $e) {
            return 'Date invalide';
        }
    }

    /**
     * Formate une date avec heure en français
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatFrenchWithTime($date)
    {
        try {
            if (!$date) {
                return 'N/A';
            }

            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            // Convertir au fuseau horaire UTC+3 (Riyadh)
            return $carbon->setTimezone('Asia/Riyadh')->locale('fr')->isoFormat('DD/MM/YYYY à HH:mm');
        } catch (\Exception $e) {
            return 'Date invalide';
        }
    }

    /**
     * Formate une date courte en français (DD/MM/YY)
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatFrenchShort($date)
    {
        return self::formatFrench($date, 'DD/MM/YY');
    }

    /**
     * Formate une date complète en français
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatFrenchFull($date)
    {
        return self::formatFrench($date, 'dddd DD MMMM YYYY');
    }

    /**
     * Formate une date pour les reçus de paiement
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatForReceipt($date)
    {
        return self::formatFrench($date, 'DD/MM/YYYY');
    }

    /**
     * Formate une date pour l'historique des paiements
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatForPaymentHistory($date)
    {
        return self::formatFrench($date, 'DD/MM/YY');
    }

    /**
     * Obtient la date actuelle formatée en français
     *
     * @param string $format
     * @return string
     */
    public static function now($format = 'DD/MM/YYYY à HH:mm')
    {
        return Carbon::now()->setTimezone('Asia/Riyadh')->locale('fr')->isoFormat($format);
    }

    /**
     * Calcule la période entre deux dates
     *
     * @param string|Carbon $startDate
     * @param string|Carbon $endDate
     * @return string
     */
    public static function formatPeriod($startDate, $endDate)
    {
        $start = self::formatForReceipt($startDate);
        $end = self::formatForReceipt($endDate);
        
        if ($start === $end) {
            return $start;
        }
        
        return $start . ' au ' . $end;
    }

    /**
     * Formate un montant en ariary avec séparateurs
     *
     * @param float|int $amount
     * @return string
     */
    public static function formatAmount($amount)
    {
        return number_format($amount, 0, ',', ' ') . ' Ar';
    }

    /**
     * Vérifie si une date est valide
     *
     * @param string $date
     * @return bool
     */
    public static function isValidDate($date)
    {
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtient le nom du mois en français
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function getMonthName($date)
    {
        try {
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            return $carbon->locale('fr')->isoFormat('MMMM');
        } catch (\Exception $e) {
            return 'Mois invalide';
        }
    }

    /**
     * Obtient le nom du jour en français
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function getDayName($date)
    {
        try {
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            return $carbon->locale('fr')->isoFormat('dddd');
        } catch (\Exception $e) {
            return 'Jour invalide';
        }
    }

    /**
     * Formate une date relative en français (il y a X jours)
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatRelative($date)
    {
        try {
            $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
            // Convertir au fuseau horaire UTC+3 (Riyadh) pour les dates relatives
            return $carbon->setTimezone('Asia/Riyadh')->locale('fr')->diffForHumans();
        } catch (\Exception $e) {
            return 'Date invalide';
        }
    }

    /**
     * Formate une date pour l'affichage dans les tableaux
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatForTable($date)
    {
        return self::formatFrench($date, 'DD/MM/YY');
    }

    /**
     * Formate une date pour les rapports
     *
     * @param string|Carbon $date
     * @return string
     */
    public static function formatForReport($date)
    {
        return self::formatFrench($date, 'DD/MM/YYYY');
    }
/**
     * Convertit un montant en lettres pour les reçus
     *
     * @param float|int $montant
     * @return string
     */
    public static function convertirMontantEnLettres($montant)
    {
        $unites = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $dizaines = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt'];
        $teens = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];

        if ($montant == 0) return 'zéro ariary';

        $montant = intval($montant);
        $result = '';

        // Millions
        if ($montant >= 1000000) {
            $millions = intval($montant / 1000000);
            $result .= self::convertirNombre($millions, $unites, $dizaines, $teens) . ' million' . ($millions > 1 ? 's' : '') . ' ';
            $montant %= 1000000;
        }

        // Milliers
        if ($montant >= 1000) {
            $milliers = intval($montant / 1000);
            $result .= self::convertirNombre($milliers, $unites, $dizaines, $teens) . ' mille ';
            $montant %= 1000;
        }

        // Centaines, dizaines, unités
        if ($montant > 0) {
            $result .= self::convertirNombre($montant, $unites, $dizaines, $teens);
        }

        return trim($result) . ' ariary';
    }

    /**
     * Convertit un nombre en lettres
     *
     * @param int $nombre
     * @param array $unites
     * @param array $dizaines
     * @param array $teens
     * @return string
     */
    protected static function convertirNombre($nombre, $unites, $dizaines, $teens)
    {
        $result = '';

        // Centaines
        if ($nombre >= 100) {
            $centaines = intval($nombre / 100);
            if ($centaines == 1) {
                $result .= 'cent ';
            } else {
                $result .= $unites[$centaines] . ' cent ';
            }
            $nombre %= 100;
        }

        // Dizaines et unités
        if ($nombre >= 20) {
            $diz = intval($nombre / 10);
            $unit = $nombre % 10;

            if ($diz == 7 || $diz == 9) {
                $result .= $dizaines[$diz] . '-' . ($diz == 7 ? $teens[$unit] : $teens[$unit]);
            } else {
                $result .= $dizaines[$diz];
                if ($unit > 0) {
                    $result .= '-' . $unites[$unit];
                }
            }
        } elseif ($nombre >= 10) {
            $result .= $teens[$nombre - 10];
        } elseif ($nombre > 0) {
            $result .= $unites[$nombre];
        }

        return $result;
    }
}