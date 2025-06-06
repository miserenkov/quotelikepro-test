<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 17:37
 */

namespace Tests\Unit\Fakes;

use App\Contracts\MoneyService;
use App\Contracts\PriceRule;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceRuleResultData;
use Brick\Math\BigDecimal;
use Closure;

class FakePlusTwoPercentPriceRule implements PriceRule
{
    public function __construct(protected MoneyService $moneyService, public string|null $bindValue = null) {}

    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
    {
        $percent = BigDecimal::of(2)
            ->toScale($this->moneyService->defaultDecimalScale())
            ->dividedBy(100, roundingMode: $this->moneyService->defaultRoundingMode());
        $value = $context->getCurrentPrice()->multipliedBy($percent);
        $newPrice = $context->getCurrentPrice()->plus($value);

        $context
            ->setCurrentPrice($newPrice)
            ->addRuleResult(new PriceRuleResultData(
                id: class_basename(self::class),
                label: self::class,
                percent: $percent,
                value: $value,
                newPrice: $newPrice,
            ));

        return $next($context);
    }
}
