<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:34
 */

namespace Tests\Unit;

use App\Services\DefaultMoneyService;
use Brick\Math\RoundingMode;
use Brick\Money\Currency;
use Brick\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DefaultMoneyServiceTest extends TestCase
{
    #[TestWith([3, 3]), TestWith([null, 2])]
    public function testServiceReturnsDefaultDecimalScale(int|null $configValue, int $expected): void
    {
        config()->set('money.decimal_scale', $configValue);

        $service = $this->app->makeWith(DefaultMoneyService::class, ['config' => config('money', [])]);

        $this->assertSame($expected, $service->defaultDecimalScale());
    }

    #[TestWith([RoundingMode::CEILING, RoundingMode::CEILING]), TestWith([null, RoundingMode::HALF_UP])]
    public function testServiceReturnsDefaultRoundingMode(RoundingMode|null $configValue, RoundingMode $expected): void
    {
        config()->set('money.rounding_mode', $configValue);

        $service = $this->app->makeWith(DefaultMoneyService::class, ['config' => config('money', [])]);

        $this->assertSame($expected, $service->defaultRoundingMode());
    }

    #[TestWith([10, 10]), TestWith([null, 1])]
    public function testServiceReturnsDefaultRoundingStep(int|null $configValue, int $expected): void
    {
        config()->set('money.rounding_step', $configValue);

        $service = $this->app->makeWith(DefaultMoneyService::class, ['config' => config('money', [])]);

        $this->assertSame($expected, $service->defaultRoundingStep());
    }

    public function testServiceReturnsDefaultCurrency(): void
    {
        config()->set('money.default_currency', 'EUR');

        $service = $this->app->makeWith(DefaultMoneyService::class, ['config' => config('money', [])]);

        $actual = $service->defaultCurrency();

        $this->assertInstanceOf(Currency::class, $actual);
        $this->assertEquals(Currency::of('EUR'), $actual);
    }

    #[TestWith([null]), TestWith(['UNK'])]
    public function testServiceThrowsExceptionWhenDefaultCurrencyIsInvalid(string|null $configValue): void
    {
        config()->set('money.default_currency', $configValue);

        $service = $this->app->makeWith(DefaultMoneyService::class, ['config' => config('money', [])]);

        $this->expectException(UnknownCurrencyException::class);

        $service->defaultCurrency();
    }
}
