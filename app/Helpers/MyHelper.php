<?php

if (!function_exists('makeCurrency')) {
    function makeCurrency(int $number, bool $decimal = false, bool $currency = true)
    {
        $classCurrency = new \App\Helpers\Currency();
        return $classCurrency->makeCurrency($number, $decimal, $currency);
    }
}