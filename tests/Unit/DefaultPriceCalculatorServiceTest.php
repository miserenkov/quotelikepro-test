<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 17:30
 */

namespace Tests\Unit;

use App\Contracts\MoneyService;
use App\DTO\PriceCalculationInput;
use App\Exceptions\PriceCalculationException;
use App\Services\DefaultPriceCalculatorService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tests\TestCase;
use Tests\Unit\Fakes\FakeMinusFivePercentPriceRule;
use Tests\Unit\Fakes\FakePlusTwoPercentPriceRule;

class DefaultPriceCalculatorServiceTest extends TestCase
{
    #[DataProvider('correctSetsProvider')]
    public function testCalculatesCorrectlyInRightOrder(float $rawBasePrice, int $quantity, float $rawExpectedTotal, array $rules): void
    {
        $basePrice = BigDecimal::of($rawBasePrice);

        $input = new PriceCalculationInput($basePrice, $quantity, 0, 0);

        $money = $this->createMock(MoneyService::class);
        $money->method('defaultCurrency')->willReturn($expectedCurrency = Currency::of('EUR'));
        $money->method('defaultDecimalScale')->willReturn(2);
        $money->method('defaultRoundingMode')->willReturn(RoundingMode::HALF_UP);
        $money->method('make')->willReturn(Money::of($basePrice, $expectedCurrency));

        $service = new DefaultPriceCalculatorService(['rules' => $rules], $money);
        $result = $service->calculate($input);

        $expectedTotal = BigDecimal::of($rawExpectedTotal);

        $this->assertTrue($result->product->price->isEqualTo($basePrice));
        $this->assertSame($result->product->quantity, $quantity);
        $this->assertTrue($result->product->total->isEqualTo($basePrice->multipliedBy($quantity)));

        $this->assertCount(count($rules), $result->appliedRules);

        $rulesId = array_map(class_basename(...), array_keys($rules));
        $this->assertSame($rulesId, $result->appliedRules->pluck('id')->all());


        $this->assertTrue($result->total->isEqualTo($expectedTotal));
        $this->assertTrue($result->total->getCurrency()->is($expectedCurrency));
    }

    public function testGetPriceRulesReturnsListOfRulesInstanceInSameOrdering(): void
    {
        $money = $this->createMock(MoneyService::class);
        $service = new DefaultPriceCalculatorService([
            'rules' => [
                FakeMinusFivePercentPriceRule::class => [
                    'bindValue' => 'Test container bind -5%',
                ],
                FakePlusTwoPercentPriceRule::class => [
                    'bindValue' => 'Test container bind 2%',
                ],
            ],
        ], $money);

        $ref = new ReflectionClass($service);
        $method = $ref->getMethod('getPriceRules');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $method->setAccessible(true);

        $rules = $method->invoke($service);

        $this->assertCount(2, $rules);

        $this->assertInstanceOf(FakeMinusFivePercentPriceRule::class, $rules[0]);
        $this->assertSame('Test container bind -5%', $rules[0]->bindValue);

        $this->assertInstanceOf(FakePlusTwoPercentPriceRule::class, $rules[1]);
        $this->assertSame('Test container bind 2%', $rules[1]->bindValue);
    }

    public function testGetPriceRulesThrowsExceptionWhenRulesNotProvided(): void
    {
        $money = $this->createMock(MoneyService::class);
        $money->method('make')->willReturn(Money::of(20, 'USD'));

        $service = new DefaultPriceCalculatorService([], $money);
        $input = new PriceCalculationInput(
            basePrice: BigDecimal::of('10'),
            quantity: 2,
            categoryId: 0,
            locationId: 0,
        );

        $this->expectException(PriceCalculationException::class);

        $service->calculate($input);
    }

    public function testGetPriceRulesThrowsExceptionWhenInvalidClass(): void
    {
        $money = $this->createMock(MoneyService::class);
        $service = new DefaultPriceCalculatorService([
            'rules' => [
                'NonExistingClass' => [],
            ],
        ], $money);

        $ref = new ReflectionClass($service);
        $method = $ref->getMethod('getPriceRules');

        /** @noinspection PhpExpressionResultUnusedInspection */
        $method->setAccessible(true);

        $this->expectException(PriceCalculationException::class);
        $method->invoke($service);
    }

    public static function correctSetsProvider(): array
    {
        return [
            [
                100.0, 3, 290.7,
                [
                    FakeMinusFivePercentPriceRule::class => [],
                    FakePlusTwoPercentPriceRule::class => [],
                ],
            ],
            [
                200.0, 5, 969.0,
                [
                    FakePlusTwoPercentPriceRule::class => [],
                    FakeMinusFivePercentPriceRule::class => [],
                ],
            ],
        ];
    }
}
