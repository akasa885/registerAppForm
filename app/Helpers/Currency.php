<?php

namespace App\Helpers;

use App\Http\Traits\FormatNumberTrait;

class Currency {

    use FormatNumberTrait;

    public function makeCurrency(int $number, bool $decimal = false, bool $currency = true)
    {
        if ($currency) {
            if ($decimal) {
                return $this->priceWithCurrencyAndDecimal($number);
            }
            return $this->priceWithCurrency($number);
        }

        if ($decimal) {
            return $this->priceWithoutCurrencyAndDecimal($number);
        }
        return $this->priceWithoutCurrency($number);
    }
}