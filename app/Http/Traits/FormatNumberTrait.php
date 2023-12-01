<?php

namespace App\Http\Traits;

trait FormatNumberTrait
{
    public function priceWithCurrency($price)
    {
        return 'IDR. ' . number_format($price, 0, ',', '.');
    }

    public function priceWithoutCurrency($price)
    {
        return number_format($price, 0, ',', '.');
    }

    public function priceWithCurrencyAndDecimal($price)
    {
        return 'IDR. ' . number_format($price, 2, ',', '.');
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

    public function shorterCounting($number)
    {
        $suffix = '';
        if ($number >= 1000 && $number < 1000000) {
            $number = round($number / 1000, 1);
            $suffix = 'K';
        } elseif ($number >= 1000000 && $number < 1000000000) {
            $number = round($number / 1000000, 1);
            $suffix = 'M';
        } elseif ($number >= 1000000000 && $number < 1000000000000) {
            $number = round($number / 1000000000, 1);
            $suffix = 'B';
        } elseif ($number >= 1000000000000) {
            $number = round($number / 1000000000000, 1);
            $suffix = 'T';
        }

        return $number . $suffix;
    }
}