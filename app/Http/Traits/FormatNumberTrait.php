<?php

namespace App\Http\Traits;

trait FormatNumberTrait
{
    public function priceWithCurrency($price)
    {
        return 'Rp. ' . number_format($price, 0, ',', '.');
    }

    public function priceWithoutCurrency($price)
    {
        return number_format($price, 0, ',', '.');
    }

    public function priceWithCurrencyAndDecimal($price)
    {
        return 'Rp. ' . number_format($price, 2, ',', '.');
    }

    public function priceWithoutCurrencyAndDecimal($price)
    {
        return number_format($price, 2, ',', '.');
    }

    public function addZeroPrefix($digits, $number)
    {
        return str_pad($number, $digits, '0', STR_PAD_LEFT);
    }

    public function changeIntegerIntoDecimalTwo($number)
    {
        return number_format($number, 2, '.', '');
    }
}