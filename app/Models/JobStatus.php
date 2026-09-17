<?php

declare(strict_types=1);

namespace App\Models;

enum JobStatus: string {
    case Applied = 'Applied';
    case Offered = 'Offered';
    case Interviewing = 'Interviewing';
    case Rejected = 'Rejected';
    case Hired = 'Hired';

    public function cssClass(): string {
        return match ($this) {
            self::Applied => 'job-applied',
            self::Offered => 'job-offered',
            self::Interviewing => 'job-interviewing',
            self::Rejected => 'job-rejected',
            self::Hired => 'job-hired',
        };
    }
}