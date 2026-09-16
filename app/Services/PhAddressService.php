<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PhAddressService
{
    protected function query()
    {
        return DB::connection('ph_address')->table('Addresses');
    }

    public function regions(): Collection
    {
        return $this->query()
            ->where('level', 'Reg')
            ->orderBy('name')
            ->get(['code', 'name']);
    }

    /**
     * Provinces AND NCR districts live at the same hierarchy position.
     */
    public function provinces(string $regionCode): Collection
    {
        $prefix = substr($regionCode, 0, 2);

        return $this->query()
            ->whereIn('level', ['Prov', 'Dist'])
            ->whereRaw('substr(code, 1, 2) = ?', [$prefix])
            ->orderBy('name')
            ->get(['code', 'name', 'level']);
    }

    /**
     * Cities, municipalities, AND NCR sub-municipalities live at the same hierarchy position.
     */
    public function cities(string $provinceCode): Collection
    {
        $prefix = substr($provinceCode, 0, 4);

        return $this->query()
            ->whereIn('level', ['Mun', 'City', 'SubMun'])
            ->whereRaw('substr(code, 1, 4) = ?', [$prefix])
            ->orderBy('name')
            ->get(['code', 'name', 'level']);
    }

    public function barangays(string $cityCode): Collection
    {
        $prefix = substr($cityCode, 0, 6);

        return $this->query()
            ->where('level', 'Bgy')
            ->whereRaw('substr(code, 1, 6) = ?', [$prefix])
            ->orderBy('name')
            ->get(['code', 'name']);
    }

    public function findByCode(string $code)
    {
        return $this->query()->where('code', $code)->first();
    }
}
