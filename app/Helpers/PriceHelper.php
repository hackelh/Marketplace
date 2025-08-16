<?php

namespace App\Helpers;

class PriceHelper
{
    // Taux de conversion fixe (1 EUR = 655.957 FCFA)
    const EUR_TO_XOF_RATE = 655.957;

    /**
     * Convertit un prix d'EUR en FCFA
     *
     * @param float $priceInEur Prix en EUR
     * @return float Prix en FCFA
     */
    public static function toXof($priceInEur)
    {
        return $priceInEur * self::EUR_TO_XOF_RATE;
    }

    /**
     * Formate un prix en FCFA
     *
     * @param float $price Prix à formater
     * @return string Prix formaté avec le symbole FCFA
     */
    public static function format($price)
    {
        return number_format($price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Convertit et formate un prix d'EUR en FCFA
     *
     * @param float $priceInEur Prix en EUR
     * @return string Prix formaté en FCFA
     */
    public static function convertAndFormat($priceInEur)
    {
        return self::format(self::toXof($priceInEur));
    }
}
