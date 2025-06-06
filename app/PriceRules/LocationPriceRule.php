<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:40
 */

namespace App\PriceRules;

use App\Contracts\LocationRepository;
use App\Contracts\MoneyService;
use App\DTO\PriceCalculationContext;
use App\DTO\PriceRuleResultData;
use App\Exceptions\PriceCalculationException;
use Closure;

class LocationPriceRule extends AbstractPriceRule
{
    public function __construct(
        protected readonly LocationRepository $repository,
        protected readonly MoneyService       $moneyService,
    ) {}

    /**
     * @throws PriceCalculationException
     */
    public function apply(PriceCalculationContext $context, Closure $next): PriceCalculationContext
    {
        $location = $this->repository->find($context->input->locationId);

        if (!$location) {
            throw new PriceCalculationException('Given location does not exist');
        }

        if ($location->price_modifier->isZero()) {
            return $next($context);
        }

        $this->ensureThatModifierIsCorrect($location->price_modifier, $location->id);

        $percent = $location->price_modifier
            ->dividedBy(100, roundingMode: $this->moneyService->defaultRoundingMode());

        $value = $context->getCurrentPrice()->multipliedBy($percent);
        $newPrice = $context->getCurrentPrice()->plus($value);

        $context
            ->setCurrentPrice($newPrice)
            ->addRuleResult(new PriceRuleResultData(
                id: class_basename(self::class),
                label: $this->buildLabel($location->price_modifier, [
                    'location' => $location->name,
                ]),
                percent: $location->price_modifier,
                value: $value,
                newPrice: $newPrice,
            ));

        return $next($context);
    }
}
