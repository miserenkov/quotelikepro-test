<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:29
 */

namespace App\DTO;

use Brick\Money\Money;
use Illuminate\Support\Collection;

class PriceCalculationContext
{
    public function __construct(
        public PriceCalculationInput $input,
        protected Money $currentPrice,
        /** @var Collection<PriceRuleResultData> */
        protected Collection $appliedRules,
    ) {}

    public function getCurrentPrice(): Money
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(Money $price): self
    {
        $this->currentPrice = $price;

        return $this;
    }

    public function addRuleResult(PriceRuleResultData $ruleData): self
    {
        $this->appliedRules->push($ruleData);

        return $this;
    }

    /**
     * @return Collection<PriceRuleResultData>
     */
    public function getAppliedRules(): Collection
    {
        return $this->appliedRules;
    }
}
