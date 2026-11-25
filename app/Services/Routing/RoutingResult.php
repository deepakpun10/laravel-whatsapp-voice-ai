<?php

namespace App\Services\Routing;

use App\Models\Business;

class RoutingResult
{
    public function __construct(
        public ?Business $business,
        public float $confidence,
        public string $status, // 'routed', 'ambiguous', 'unassigned_review', 'known_contact'
        public string $reason,
        public array $matchedKeywords = [],
        public array $details = []
    ) {}

    public function isConfident(): bool
    {
        return $this->status === 'routed' && $this->business !== null;
    }
}
