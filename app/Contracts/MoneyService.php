<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:19
 */

namespace App\Contracts;

use Brick\Math\RoundingMode;
use Brick\Money\Context;
use Brick\Money\Currency;

interface MoneyService
{
    public function defaultDecimalScale(): int;

    public function defaultRoundingMode(): RoundingMode;

    public function defaultRoundingStep(): int;

    public function defaultCurrency(): Currency;

    public function defaultContext(): Context;
}
