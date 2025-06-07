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
use Illuminate\Support\Collection;

class QuantityPriceRule extends AbstractPriceRule
{
    public function __construct(protected readonly array $rules, protected readonly MoneyService $moneyService) {}

    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
    {
        if (empty($this->rules)) {
            return $next($context);
        }

        $firstSuitableQuantity = $this->getSortedQuantityRules()
            ->firstWhere('quantity', '<', $context->input->quantity);

        if ($firstSuitableQuantity === null) {
            return $next($context);
        }

        $percent = $firstSuitableQuantity['percentModifier']
            ->dividedBy(100, roundingMode: $this->moneyService->defaultRoundingMode());

        $value = $context->getCurrentPrice()->multipliedBy($percent, $this->moneyService->defaultRoundingMode());
        $newPrice = $context->getCurrentPrice()->plus($value, $this->moneyService->defaultRoundingMode());

        $context
            ->setCurrentPrice($newPrice)
            ->addRuleResult(new PriceRuleResultData(
                id: class_basename(self::class),
                label: $this->buildLabel($firstSuitableQuantity['percentModifier'], [
                    'quantity' => $firstSuitableQuantity['quantity'],
                ]),
                percent: $firstSuitableQuantity['percentModifier'],
                value: $value,
                newPrice: $newPrice,
            ));

        return $next($context);
    }

    /**
     * @return Collection<array{quantity: int, percentModifier: BigDecimal}>
     */
    protected function getSortedQuantityRules(): Collection
    {
        return collect($this->rules)
            ->map(function (mixed $rule) {
                if (!is_array($rule) || !isset($rule['quantity'], $rule['percentModifier'])) {
                    throw new PriceCalculationException('Invalid quantity rule configuration');
                }

                $modifier = BigDecimal::of($rule['percentModifier'])
                    ->toScale($this->moneyService->defaultDecimalScale(), $this->moneyService->defaultRoundingMode());

                $this->ensureThatModifierIsCorrect($modifier);

                return [
                    'quantity' => $rule['quantity'],
                    'percentModifier' => $modifier,
                ];
            })
            ->sortByDesc('quantity');
    }
}
