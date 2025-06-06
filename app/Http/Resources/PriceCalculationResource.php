<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:38
 */

namespace App\Http\Resources;

use App\DTO\PriceCalculationResultData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin PriceCalculationResultData
 */
class PriceCalculationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'number' => Str::random(8),
            'product' => $this->product,
            'appliedRules' => $this->appliedRules,
            'total' => $this->total->toFloat(),
            'currency' => $this->currency->getCurrencyCode(),
        ];
    }
}
