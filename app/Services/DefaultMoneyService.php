<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:22
 */

namespace App\Services;

use App\Contracts\MoneyService;
use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Currency;
use Brick\Money\Exception\UnknownCurrencyException;

class DefaultMoneyService implements MoneyService
{
    public function __construct(protected readonly array $config) {}

    public function defaultDecimalScale(): int
    {
        return (int) ($this->config['decimal_scale'] ?? 2);
    }

    public function defaultRoundingMode(): RoundingMode
    {
        return $this->config['rounding_mode'] ?? RoundingMode::HALF_UP;
    }

    public function defaultRoundingStep(): int
    {
        return (int) ($this->config['rounding_step'] ?? 1);
    }

    /**
     * @throws UnknownCurrencyException
     */
    public function defaultCurrency(): Currency
    {
        return Currency::of($this->config['default_currency'] ?? '');
    }

    public function defaultContext(): Context
    {
        return new Context\CustomContext($this->defaultDecimalScale(), $this->defaultRoundingStep());
    }
}
