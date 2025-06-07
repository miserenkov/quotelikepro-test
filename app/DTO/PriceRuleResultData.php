<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:29
 */

namespace App\DTO;

use Brick\Math\BigDecimal;
use Brick\Money\Money;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

readonly class PriceRuleResultData implements Arrayable, JsonSerializable
{
    public function __construct(
        public string     $id,
        public string     $label,
        public BigDecimal $percent,
        public Money $value,
        public Money $newPrice,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'percent' => $this->percent->toFloat(),
            'value' => $this->value->getAmount()->toFloat(),
            'newPrice' => $this->newPrice->getAmount()->toFloat(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
