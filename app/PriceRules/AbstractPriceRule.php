<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:43
 */

namespace App\PriceRules;

use App\Contracts\PriceRule;
use App\Exceptions\PriceCalculationException;
use Brick\Math\BigDecimal;

abstract class AbstractPriceRule implements PriceRule
{
    protected function ensureThatModifierIsCorrect(BigDecimal $modifier, int|null $recordId = null): void
    {
        if ($modifier->isLessThan(-100) || $modifier->isGreaterThan(100)) {
            throw PriceCalculationException::invalidPercentModifier((string) $modifier, static::class, $recordId);
        }
    }

    protected function buildLabel(BigDecimal $modifier, array $replace = []): string
    {
        $prefix = $modifier->isPositive() ? __('priceCalculation.Surcharge') : __('priceCalculation.Discount');

        return __('priceCalculation.rules.' . class_basename(static::class), [
            'prefix' => $prefix,
            'modifier' => (string) $modifier->abs(),
            ...$replace,
        ]);
    }
}
