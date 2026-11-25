<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class TenantContext
{
    public const SESSION_KEY = 'active_business_id';

    public static function getActiveBusinessId(): ?int
    {
        $id = Session::get(self::SESSION_KEY);

        return $id ? (int) $id : null;
    }

    public static function setActiveBusinessId(?int $businessId): void
    {
        if ($businessId === null || $businessId === 0) {
            Session::forget(self::SESSION_KEY);
        } else {
            Session::put(self::SESSION_KEY, $businessId);
        }
    }

    public static function getActiveBusiness(): ?Business
    {
        $id = self::getActiveBusinessId();

        if (! $id) {
            return null;
        }

        return Business::find($id);
    }

    public static function getAllBusinesses(): Collection
    {
        return Business::where('status', 'active')->orderBy('name')->get();
    }
}
