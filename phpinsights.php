<?php

declare(strict_types=1);

return [
    'preset' => 'default',
    'remove' => [
        PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer::class,
    ],
    'exclude' => [
        'app/routes.php',
        'storage'
    ],
];