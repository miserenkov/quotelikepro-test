<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:40
 */

namespace App\PriceRules;

use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceRuleResultData;
use App\Exceptions\PriceCalculationException;
use Brick\Math\BigDecimal;
use Closure;

class SellerPriceRule extends AbstractPriceRule
{
    public function __construct(protected readonly float $percentModifier, protected readonly MoneyService $moneyService) {}

    /**
     * @throws PriceCalculationException
     */
    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
    {
        $percentModifier = $this->getPercentModifier();

        if ($percentModifier->isZero()) {
            return $next($context);
        }

        $this->ensureThatModifierIsCorrect($percentModifier);

        $percent = $percentModifier
            ->dividedBy(100, roundingMode: $this->moneyService->defaultRoundingMode());

        $value = $context->getCurrentPrice()->multipliedBy($percent);
        $newPrice = $context->getCurrentPrice()->plus($value);

        $context
            ->setCurrentPrice($newPrice)
            ->addRuleResult(new PriceRuleResultData(
                id: class_basename(self::class),
                label: $this->buildLabel($percentModifier),
                percent: $percentModifier,
                value: $value,
                newPrice: $newPrice,
            ));

        return $next($context);
    }

    protected function getPercentModifier(): BigDecimal
    {
        return BigDecimal::of($this->percentModifier)
            ->toScale($this->moneyService->defaultDecimalScale(), $this->moneyService->defaultRoundingMode());
    }
}
