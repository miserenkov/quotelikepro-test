<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:48
 */

namespace Tests\Unit;

use App\Casts\DecimalCast;
use App\Contracts\MoneyService;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase;

class DecimalCastTest extends TestCase
{
    public function testGetScalesValueBasedOnGivenPrecision(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(2);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(2, false);
        $model = $this->createMock(Model::class);

        $rawValue = '123.456';
        $result = $cast->get($model, 'amount', $rawValue, []);

        $expected = BigDecimal::of($rawValue)->toScale(2, RoundingMode::HALF_UP);

        $this->assertTrue($expected->isEqualTo($result));
    }

    public function testGetUsesDefaultPrecisionWhenNoneProvided(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(3);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(null, false);
        $model = $this->createMock(Model::class);

        $rawValue = '1.23456';
        $result = $cast->get($model, 'amount', $rawValue, []);

        $expected = BigDecimal::of($rawValue)->toScale(3, RoundingMode::HALF_UP);

        $this->assertTrue($expected->isEqualTo($result));
    }

    public function testGetReturnsNullWhenNullableAndValueIsNull(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(2);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(2, true);
        $model = $this->createMock(Model::class);

        $result = $cast->get($model, 'amount', null, []);
        $this->assertNull($result);
    }

    public function testSetReturnsStringValueWhenBigDecimalProvided(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(2);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(2, false);
        $model = $this->createMock(Model::class);
        $decimalVal = BigDecimal::of('7.891');

        $result = $cast->set($model, 'amount', $decimalVal, []);

        $this->assertArrayHasKey('amount', $result);

        $expectedString = (string) $decimalVal->toScale(2, RoundingMode::HALF_UP);
        $this->assertSame($expectedString, $result['amount']);
    }

    public function testSetConvertsStringToBigDecimalAndScales(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(2);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(2, false);
        $model = $this->createMock(Model::class);

        $rawValue = '4.567';
        $result = $cast->set($model, 'amount', $rawValue, []);

        $expected = (string) BigDecimal::of($rawValue)->toScale(2, RoundingMode::HALF_UP);

        $this->assertArrayHasKey('amount', $result);
        $this->assertSame($expected, $result['amount']);
    }

    public function testSetReturnsNullArrayWhenNullableAndValueNull(): void
    {
        $moneyService = $this->createMock(MoneyService::class);
        $moneyService
            ->method('defaultDecimalScale')
            ->willReturn(2);
        $moneyService
            ->method('defaultRoundingMode')
            ->willReturn(RoundingMode::HALF_UP);

        $this->app->instance(MoneyService::class, $moneyService);

        $cast = new DecimalCast(2, true);
        $model = $this->createMock(Model::class);

        $result = $cast->set($model, 'amount', null, []);
        $this->assertSame(['amount' => null], $result);
    }
}
