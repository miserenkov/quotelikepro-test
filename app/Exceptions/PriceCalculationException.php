<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:00
 */

namespace App\Exceptions;

use App\Contracts\PriceRule;
use Exception;

class PriceCalculationException extends Exception
{
    public static function rulesNotProvided(): self
    {
        return new self('Price rules not provided to PriceCalculator.');
    }

    public static function invalidPriceRuleClass(string $ruleClassName): self
    {
        return new self(sprintf(
            'Price rule class `%s` does not exist or does not implement `%s`.',
            $ruleClassName,
            PriceRule::class,
        ));
    }

    public static function invalidPercentModifier(string $percentModifier, string $ruleClassName, int|null $recordId = null): self
    {
        $recordLabel = $recordId ? sprintf('(record ID: %d)', $recordId) : null;

        return new self(sprintf(
            'Invalid percent modifier "%s" in rule "%s"%s. Percent must be between -100 and 100.',
            $percentModifier,
            $ruleClassName,
            $recordLabel,
        ));
    }
}
