<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 17:00
 */

namespace Tests\Unit\PriceRules;

use App\Contracts\LocationRepository;
use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceCalculationInput;
use App\DTO\PriceRuleResultData;
use App\Exceptions\PriceCalculationException;
use App\Models\Location;
use App\PriceRules\LocationPriceRule;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class LocationPriceRuleTest extends TestCase
{
    public function testRuleThrowsExceptionWhenLocationNotFound(): void
    {
        $repo = $this->createMock(LocationRepository::class);
        $repo->method('find')->willReturn(null);

        $money = $this->createMock(MoneyService::class);

        $rule = new LocationPriceRule($repo, $money);
        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 888, 0);

        $this->expectException(PriceCalculationException::class);
        $rule->apply($context, fn ($c) => $c);
    }

    public function testRuleIsSkippedWhenModifierIsZero(): void
    {
        $location = new Location(['id' => 78, 'price_modifier' => 0]);
        $repo = $this->createMock(LocationRepository::class);
        $repo
            ->method('find')
            ->with(78)
            ->willReturn($location);

        $money = $this->createMock(MoneyService::class);

        $rule = new LocationPriceRule($repo, $money);

        $context = $this->createMock(PriceCalculationContext::class);
        $context->input = new PriceCalculationInput(BigDecimal::of(0), 0, 0, 78);
        $context->expects($this->never())->method('setCurrentPrice');
        $context->expects($this->never())->method('addRuleResult');

        $returned = $rule->apply($context, fn ($c) => $c);

        $this->assertSame($context, $returned);
    }

    #[TestWith([-15.0, 425.0]), TestWith([35.0, 675.0])]
    public function testAppliesRuleCorrectly(float $priceModifier, float $rawExpectedPrice): void
    {
        $location = new Location(['price_modifier' => $priceModifier]);

        $repo = $this->createMock(LocationRepository::class);
        $repo->method('find')->willReturn($location);

        $money = $this->createMock(MoneyService::class);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);

        $rule = new LocationPriceRule($repo, $money);
        $initialPrice = BigDecimal::of('500');

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

        $this->assertSame('LocationPriceRule', $appliedRule->id);
        $this->assertTrue($appliedRule->percent->isEqualTo($priceModifier));
        $this->assertTrue($appliedRule->newPrice->isEqualTo($expectedPrice));
    }
}
