<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 06.06.2025 14:44
 */

use App\PriceRules;

return [
    'rules' => [
        PriceRules\CategoryPriceRule::class => [],
        PriceRules\LocationPriceRule::class => [],
        PriceRules\SellerPriceRule::class => [
            'percentModifier' => -2,
        ],
        PriceRules\QuantityPriceRule::class => [
            'rules' => [
                ['quantity' => 10, 'percentModifier' => -5],
            ],
        ],
    ],
];
