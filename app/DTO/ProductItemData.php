<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:34
 */

namespace App\DTO;

use Brick\Math\BigDecimal;
use Brick\Money\Money;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

readonly class ProductItemData implements Arrayable, JsonSerializable
{
    public function __construct(
        public string $name,
        public Money  $price,
        public int    $quantity,
        public Money  $total,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price->getAmount()->toFloat(),
            'quantity' => $this->quantity,
            'total' => $this->total->getAmount()->toFloat(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
