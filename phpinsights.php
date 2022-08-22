<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'remove' => [
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\FunctionLengthSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
    ],
    'config' => [
    ],
    'exclude' => [
        'app/routes.php',
        'storage'
    ],
];