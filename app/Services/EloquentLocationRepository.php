<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:26
 */

namespace App\Services;

use App\Contracts\LocationRepository;
use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class EloquentLocationRepository implements LocationRepository
{
    public function getAll(): Collection
    {
        return Location::query()->get();
    }

    public function find(int $id): ?Location
    {
        return Location::query()->find($id);
    }
}
