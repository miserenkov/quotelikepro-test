<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 16:23
 */

namespace Tests\Unit\PriceRules;

use App\DTO\PriceCalculationContext;
use App\Exceptions\PriceCalculationException;
use App\PriceRules\AbstractPriceRule;
use Brick\Math\BigDecimal;
use Closure;
use Exception;
use Tests\TestCase;

class AbstractPriceRuleTest extends TestCase
{
    public function testEnsureDoesNotThrowForValidModifiers(): void
    {
        $instance = $this->getInstance();

        $instance->ensureThatModifierIsCorrect(BigDecimal::of(-100));
        $instance->ensureThatModifierIsCorrect(BigDecimal::of(0));
        $instance->ensureThatModifierIsCorrect(BigDecimal::of(100));
        $instance->ensureThatModifierIsCorrect(BigDecimal::of(53.1));

        /* @phpstan-ignore method.alreadyNarrowedType */
        $this->assertTrue(true);
    }

    public function testEnsureThrowsWhenModifierLessThanMinusHundred(): void
    {
        $this->expectException(PriceCalculationException::class);

        $instance = $this->getInstance();
        $instance->ensureThatModifierIsCorrect(BigDecimal::of('-100.01'));
    }

    public function testEnsureThrowsWhenModifierGreaterThanHundred(): void
    {
        $this->expectException(PriceCalculationException::class);

        $instance = $this->getInstance();
        $instance->ensureThatModifierIsCorrect(BigDecimal::of(100.01));
    }

    protected function getInstance()
    {
        return new class extends AbstractPriceRule {
            public function ensureThatModifierIsCorrect(BigDecimal $modifier, ?int $recordId = null): void
            {
                parent::ensureThatModifierIsCorrect($modifier, $recordId);
            }

            public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
            {
                throw new Exception('Method not implemented');
            }
        };
    }
}
