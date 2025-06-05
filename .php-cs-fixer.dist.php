<?php

declare(strict_types=1);

/**
 * Created by PhpStorm.
 * Author: Misha Serenkov
 * Email: mi.serenkov@gmail.com
 * Date: 05.06.2025 18:33
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PER-CS2.0' => true,
    '@PER-CS2.0:risky' => true,
    'function_declaration' => [
        'closure_fn_spacing' => 'one',
    ],
];

$finder = Finder::create()
    ->in([
        'app',
        'bootstrap',
        'config',
        'database',
        'routes',
        'tests',
    ])
    ->name('*.php')
    ->notName(['**/*.blade.php'])
    ->notPath('#cache#')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
