<?php

declare(strict_types=1);

namespace App\Models;

enum Currency: string {
    case PHP = 'PHP';
    case USD = 'USD';
    case EUR = 'EUR';
    case JPY = 'JPY';
    case GBP = 'GBP';
    case CAD = 'CAD';
    case AUD = 'AUD';
    case NZD = 'NZD';

    public function flag(): string {
        return match ($this) {
            self::PHP => '🇵🇭',
            self::USD => '🇺🇸',
            self::EUR => '🇪🇺',
            self::JPY => '🇯🇵',
            self::GBP => '🇬🇧',
            self::CAD => '🇨🇦',
            self::AUD => '🇦🇺',
            self::NZD => '🇳🇿',
        };
    }
}