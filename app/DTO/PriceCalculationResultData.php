<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:33
 */

namespace App\DTO;

use Brick\Money\Money;
use Illuminate\Support\Collection;

readonly class PriceCalculationResultData
{
    public function __construct(
        public ProductItemData $product,
        /** @var Collection<PriceRuleResultData> $appliedRules */
        public Collection $appliedRules,
        public Money $total,
    ) {}
}
