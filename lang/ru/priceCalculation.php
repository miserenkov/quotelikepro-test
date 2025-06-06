<?php

declare(strict_types=1);

return [
    'Discount' => 'Скидка',
    'Surcharge' => 'Наценка',
    'rules' => [
        'CategoryPriceRule' => ':prefix :modifier% за категорию ":category"',
        'LocationPriceRule' => ':prefix :modifier% за локацию ":location"',
        'SellerPriceRule' => ':prefix продавца :modifier%',
        'QuantityPriceRule' => ':prefix :modifier% количество товара больше :quantity',
    ],
];
