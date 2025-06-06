<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 19:54
 */

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepository
{
    /**
     * @return Collection<Category>
     */
    public function getAll(): Collection;

    public function find(int $id): ?Category;
}
