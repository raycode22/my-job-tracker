<?php
declare(strict_types=1);

use App\Models\Currency;

function formatSalary(?float $amount, Currency $currency): string {
    if($amount === null) {
        return 'Not specified';
    }

    $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, $currency->value);
}