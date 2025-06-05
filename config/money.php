<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 23:20
 */

return [
    'decimal_scale' => 2,

    /** @see Brick\Math\RoundingMode */
    'rounding_mode' => Brick\Math\RoundingMode::HALF_UP,

    // Cash rounding step. Must be a multiple of 2 and/or 5.
    'rounding_step' => 1,

    'default_currency' => 'USD',
];
