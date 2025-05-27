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
            return $carbon->locale('fr')->isoFormat($format);
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
        return self::formatFrench($date, 'DD/MM/YYYY à HH:mm');
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
        return Carbon::now()->locale('fr')->isoFormat($format);
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
            return $carbon->locale('fr')->diffForHumans();
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
}