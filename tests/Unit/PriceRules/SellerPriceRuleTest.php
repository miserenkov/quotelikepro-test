<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 17:04
 */

namespace Tests\Unit\PriceRules;

use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceCalculationInput;
use App\DTO\PriceRuleResultData;
use App\PriceRules\SellerPriceRule;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class SellerPriceRuleTest extends TestCase
{
    public function testRuleIsSkippedWhenModifierIsZero(): void
    {
        $money = $this->createMock(MoneyService::class);
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new SellerPriceRule(0, $money);

        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 0, 0);
        $context->expects($this->never())->method('setCurrentPrice');
        $context->expects($this->never())->method('addRuleResult');

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertSame($context, $returned);
    }

    #[TestWith([-2.0, 392.0]), TestWith([4, 416.0])]
    public function testAppliesRuleCorrectly(float $priceModifier, float $rawExpectedPrice): void
    {
        $money = $this->createMock(MoneyService::class);
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new SellerPriceRule($priceModifier, $money);
        $initialPrice = BigDecimal::of('400');

        $context = new PriceCalculationContext(
            new PriceCalculationInput($initialPrice, 1, 34, 0),
            Money::of($initialPrice, 'USD'),
            collect(),
        );

        $expectedPrice = BigDecimal::of($rawExpectedPrice);

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertTrue($returned->getCurrentPrice()->isEqualTo($expectedPrice));
        $this->assertCount(1, $returned->getAppliedRules());

        /** @var PriceRuleResultData $appliedRule */
        $appliedRule = $returned->getAppliedRules()->first();

        $this->assertSame('SellerPriceRule', $appliedRule->id);
        $this->assertTrue($appliedRule->percent->isEqualTo($priceModifier));
        $this->assertTrue($appliedRule->newPrice->isEqualTo($expectedPrice));
    }
}
