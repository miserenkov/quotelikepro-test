<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:41
 */

namespace App\Contracts;

use App\DTO\PriceCalculationInput;
use App\DTO\PriceCalculationResultData;

interface PriceCalculatorService
{
    public function calculate(PriceCalculationInput $data): PriceCalculationResultData;
}
