<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 16:31
 */

namespace Tests\Unit\PriceRules;

use App\Contracts\CategoryRepository;
use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceCalculationInput;
use App\DTO\PriceRuleResultData;
use App\Exceptions\PriceCalculationException;
use App\Models\Category;
use App\PriceRules\CategoryPriceRule;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class CategoryPriceRuleTest extends TestCase
{
    public function testRuleThrowsExceptionWhenCategoryNotFound(): void
    {
        $repo = $this->createMock(CategoryRepository::class);
        $repo->method('find')->willReturn(null);

        $money = $this->createMock(MoneyService::class);

        $rule = new CategoryPriceRule($repo, $money);
        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 999, 0);

        $this->expectException(PriceCalculationException::class);
        $rule->apply($context, fn ($c) => $c);
    }

    public function testRuleIsSkippedWhenModifierIsZero(): void
    {
        $category = new Category(['id' => 34, 'price_modifier' => 0]);
        $repo = $this->createMock(CategoryRepository::class);
        $repo
            ->method('find')
            ->with(34)
            ->willReturn($category);

        $money = $this->createMock(MoneyService::class);

        $rule = new CategoryPriceRule($repo, $money);

        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 34, 0);
        $context->expects($this->never())->method('setCurrentPrice');
        $context->expects($this->never())->method('addRuleResult');

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertSame($context, $returned);
    }

    #[TestWith([-5.0, 190.0]), TestWith([10.0, 220.0])]
    public function testAppliesRuleCorrectly(float $priceModifier, float $rawExpectedPrice): void
    {
        $category = new Category(['price_modifier' => $priceModifier]);

        $repo = $this->createMock(CategoryRepository::class);
        $repo->method('find')->willReturn($category);

        $money = $this->createMock(MoneyService::class);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new CategoryPriceRule($repo, $money);
        $initialPrice = BigDecimal::of('200');

        $context = new PriceCalculationContext(
            new PriceCalculationInput($initialPrice, 1, 34, 0),
            $initialPrice,
            collect(),
        );

        $expectedPrice = BigDecimal::of($rawExpectedPrice);

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertTrue($expectedPrice->isEqualTo($returned->getCurrentPrice()));
        $this->assertCount(1, $returned->getAppliedRules());

        /** @var PriceRuleResultData $appliedRule */
        $appliedRule = $returned->getAppliedRules()->first();

        $this->assertSame('CategoryPriceRule', $appliedRule->id);
        $this->assertTrue($appliedRule->percent->isEqualTo($priceModifier));
        $this->assertTrue($appliedRule->newPrice->isEqualTo($expectedPrice));
    }
}
