<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:28
 */

namespace App\Contracts;

use App\DTO\PriceCalculationContext;
use Closure;

interface PriceRule
{
    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext;
}
