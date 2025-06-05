<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:20
 */

namespace App\Casts;

use App\Contracts\MoneyService;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class DecimalCast implements CastsAttributes
{
    public function __construct(protected int|null $precision = null, protected bool $nullable = false)
    {
        if ($precision === -1 || $precision === null) {
            $this->precision = $this->money()->defaultDecimalScale();
        }
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?BigDecimal
    {
        if ($this->nullable && is_null($value)) {
            return null;
        }

        return BigDecimal::of($value)->toScale($this->precision, $this->money()->defaultRoundingMode());
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($this->nullable && is_null($value)) {
            return [$key => null];
        }

        if (!$value instanceof BigDecimal) {
            $value = $this->get($model, $key, $value, $attributes);
        }

        return [
            $key => (string) ($value->toScale($this->precision, $this->money()->defaultRoundingMode())),
        ];
    }

    protected function money(): MoneyService
    {
        return app(MoneyService::class);
    }
}
