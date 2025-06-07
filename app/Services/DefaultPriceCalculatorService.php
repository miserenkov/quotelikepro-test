<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:41
 */

namespace App\Services;

use App\Contracts\MoneyService;
use App\Contracts\PriceCalculatorService;
use App\Contracts\PriceRule;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceCalculationInput;
use App\DTO\PriceCalculationResultData;
use App\DTO\ProductItemData;
use App\Exceptions\PriceCalculationException;
use Illuminate\Pipeline\Pipeline;

class DefaultPriceCalculatorService implements PriceCalculatorService
{
    public function __construct(
        protected readonly array $config,
        protected readonly MoneyService $moneyService,
    ) {}

    /**
     * @throws PriceCalculationException
     */
    public function calculate(PriceCalculationInput $data): PriceCalculationResultData
    {
        $productBasePrice = $this->moneyService->make($data->basePrice);
        $product = new ProductItemData(
            name: 'Product 1',
            price: $productBasePrice,
            quantity: $data->quantity,
            total: $productBasePrice->multipliedBy($data->quantity),
        );

        $context = new PriceCalculationContext(
            input: $data,
            currentPrice: $product->total,
            appliedRules: collect(),
        );

        /** @var PriceCalculationContext $context */
        $context = app(Pipeline::class)
            ->send($context)
            ->through($this->getPriceRules())
            ->via('apply')
            ->thenReturn();

        return new PriceCalculationResultData(
            product: $product,
            appliedRules: $context->getAppliedRules(),
            total: $context->getCurrentPrice(),
        );
    }

    protected function getPriceRules(): array
    {
        $rules = $this->config['rules'] ?? [];

        if (!is_array($rules) || count($rules) === 0) {
            throw PriceCalculationException::rulesNotProvided();
        }

        $resolvedRules = [];

        foreach ($rules as $ruleClassName => $ruleConfig) {
            if (!class_exists($ruleClassName) || !is_a($ruleClassName, PriceRule::class, true)) {
                throw PriceCalculationException::invalidPriceRuleClass($ruleClassName);
            }

            $resolvedRules[] = app()->makeWith($ruleClassName, $ruleConfig);
        }

        return $resolvedRules;
    }
}
