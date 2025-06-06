<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:30
 */

namespace App\Http\Requests;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriceCalculationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'basePrice' => ['required', 'decimal:0,2', 'min:0.01', 'max:999999999.99'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999999999'],
            'categoryId' => ['required', 'integer', Rule::exists(Category::class, 'id')],
            'locationId' => ['required', 'integer', Rule::exists(Location::class, 'id')],
        ];
    }

    public function attributes(): array
    {
        return [
            'basePrice' => __('validation.attributes.basePrice'),
            'quantity' => __('validation.attributes.quantity'),
            'categoryId' => __('validation.attributes.categoryId'),
            'locationId' => __('validation.attributes.locationId'),
        ];
    }

    public function messages(): array
    {
        return [
            'basePrice.required' => __('validation.basePrice.required'),
            'basePrice.decimal' => __('validation.basePrice.decimal'),
            'basePrice.min' => __('validation.basePrice.min'),
            'basePrice.max' => __('validation.basePrice.max'),

            'quantity.required' => __('validation.quantity.required'),
            'quantity.integer' => __('validation.quantity.integer'),
            'quantity.min' => __('validation.quantity.min'),
            'quantity.max' => __('validation.quantity.max'),

            'categoryId.required' => __('validation.categoryId.required'),
            'categoryId.exists' => __('validation.categoryId.exists'),

            'locationId.required' => __('validation.locationId.required'),
            'locationId.exists' => __('validation.locationId.exists'),
        ];
    }
}
