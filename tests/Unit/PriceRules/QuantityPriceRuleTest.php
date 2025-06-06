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
use App\Exceptions\PriceCalculationException;
use App\PriceRules\QuantityPriceRule;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class QuantityPriceRuleTest extends TestCase
{
    public function testRuleIsSkippedWhenRulesEmpty(): void
    {
        $money = $this->createMock(MoneyService::class);

        $rule = new QuantityPriceRule([], $money);

        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 0, 0);
        $context->expects($this->never())->method('setCurrentPrice');
        $context->expects($this->never())->method('addRuleResult');

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertSame($context, $returned);
    }

    #[TestWith([5]), TestWith([10])]
    public function testRuleIsSkippedWhenNoSuitableRule(int $quantity): void
    {
        $rules = [
            ['quantity' => 10, 'percentModifier' => 5],
            ['quantity' => 20, 'percentModifier' => 10],
        ];

        $money = $this->createMock(MoneyService::class);
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new QuantityPriceRule($rules, $money);

        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(100), $quantity, 0, 0);
        $context->expects($this->never())->method('setCurrentPrice');
        $context->expects($this->never())->method('addRuleResult');

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertSame($context, $returned);
    }

    public function testRuleThrowsExceptionForInvalidRuleConfig(): void
    {
        $rules = [
            ['foo' => 'bar'],
        ];

        $money = $this->createMock(MoneyService::class);
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);
        $rule = new QuantityPriceRule($rules, $money);
        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(100), 100, 0, 0);

        $this->expectException(PriceCalculationException::class);

        $rule->apply($context, fn ($c) => $c);
    }

    #[DataProvider('correctSetsProvider')]
    public function testAppliesRuleCorrectly(int $quantity, float $priceModifier, $rawExpectedPrice): void
    {
        $rules = [
            ['quantity' => $quantity, 'percentModifier' => $priceModifier],
            ['quantity' => $quantity + 1, 'percentModifier' => 20],
            ['quantity' => $quantity - 1, 'percentModifier' => 30],
        ];
        $money = $this->createMock(MoneyService::class);
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new QuantityPriceRule($rules, $money);

        $initialPrice = BigDecimal::of('500')->multipliedBy($quantity);

        $context = new PriceCalculationContext(
            new PriceCalculationInput($initialPrice, $quantity + 1, 0, 0),
            $initialPrice,
            collect(),
        );

        $expectedPrice = BigDecimal::of($rawExpectedPrice);

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertTrue($expectedPrice->isEqualTo($returned->getCurrentPrice()));
        $this->assertCount(1, $returned->getAppliedRules());

        /** @var PriceRuleResultData $appliedRule */
        $appliedRule = $returned->getAppliedRules()->first();

        $this->assertSame('QuantityPriceRule', $appliedRule->id);
        $this->assertTrue($appliedRule->percent->isEqualTo($priceModifier));
        $this->assertTrue($appliedRule->newPrice->isEqualTo($expectedPrice));
    }

    public static function correctSetsProvider(): array
    {
        return [
            [10, -10, 4500.0],
            [3, 4, 1560.0],
        ];
    }
}
