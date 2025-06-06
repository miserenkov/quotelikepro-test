<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:30
 */

namespace App\Http\Controllers;

use App\Contracts\MoneyService;
use App\Contracts\PriceCalculatorService;
use App\DTO\PriceCalculationInput;
use App\Http\Requests\PriceCalculationRequest;
use App\Http\Resources\PriceCalculationResource;
use Brick\Math\BigDecimal;

class PriceCalculationController extends Controller
{
    public function __construct(
        protected readonly MoneyService $moneyService,
        protected readonly PriceCalculatorService $priceCalculatorService,
    ) {}

    public function calculate(PriceCalculationRequest $request): PriceCalculationResource
    {
        $data = new PriceCalculationInput(
            basePrice: BigDecimal::of($request->input('basePrice'))
                ->toScale($this->moneyService->defaultDecimalScale(), $this->moneyService->defaultRoundingMode()),
            quantity: $request->integer('quantity'),
            categoryId: $request->integer('categoryId'),
            locationId: $request->integer('locationId'),
        );

        return new PriceCalculationResource($this->priceCalculatorService->calculate($data));
    }
}
