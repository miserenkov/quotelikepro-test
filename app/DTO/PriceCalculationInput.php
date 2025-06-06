<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:26
 */

namespace App\DTO;

use Brick\Math\BigDecimal;

readonly class PriceCalculationInput
{
    public function __construct(
        public BigDecimal $basePrice,
        public int        $quantity,
        public int        $categoryId,
        public int        $locationId,
    ) {}
}
