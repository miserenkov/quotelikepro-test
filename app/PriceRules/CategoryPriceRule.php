<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:30
 */

namespace App\PriceRules;

use App\Contracts\CategoryRepository;
use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceRuleResultData;
use App\Exceptions\PriceCalculationException;
use Closure;

class CategoryPriceRule extends AbstractPriceRule
{
    public function __construct(
        protected readonly CategoryRepository $repository,
        protected readonly MoneyService       $moneyService,
    ) {}

    /**
     * @throws PriceCalculationException
     */
    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
    {
        $category = $this->repository->find($context->input->categoryId);

        if (!$category) {
            throw new PriceCalculationException('Given category does not exist');
        }

        if ($category->price_modifier->isZero()) {
            return $next($context);
        }

        $this->ensureThatModifierIsCorrect($category->price_modifier, $category->id);

        $percent = $category->price_modifier
            ->dividedBy(100, roundingMode: $this->moneyService->defaultRoundingMode());

        $value = $context->getCurrentPrice()->multipliedBy($percent, $this->moneyService->defaultRoundingMode());
        $newPrice = $context->getCurrentPrice()->plus($value, $this->moneyService->defaultRoundingMode());

        $context
            ->setCurrentPrice($newPrice)
            ->addRuleResult(new PriceRuleResultData(
                id: class_basename(self::class),
                label: $this->buildLabel($category->price_modifier, [
                    'category' => $category->name,
                ]),
                percent: $category->price_modifier,
                value: $value,
                newPrice: $newPrice,
            ));

        return $next($context);
    }
}
