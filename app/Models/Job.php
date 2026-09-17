<?php

declare(strict_types=1);

namespace App\Models;

class Job { 
        public function __construct(
        public int $id, 
        public string $title, 
        public string $company,
        public JobStatus $status,
        public ?float $salary,
        public Currency $currency
    ) {}    
}