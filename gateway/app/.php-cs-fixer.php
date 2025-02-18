<?php

$finder = PhpCsFixer\Finder::create()
    ->in([__DIR__.'/src'])
    ->name('*.php')
    ->exclude(['var', 'vendor'])
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return new PhpCsFixer\Config()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'yoda_style' => false,
        'protected_to_private' => false,
        'align_multiline_comment' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'explicit_indirect_variable' => true,
        'method_chaining_indentation' => true,
        'multiline_comment_opening_closing' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_superfluous_elseif' => true,
        'phpdoc_summary' => false,
        'no_superfluous_phpdoc_tags' => false,
        'fully_qualified_strict_types' => true,
        'declare_strict_types' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments']],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder);
