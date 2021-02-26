<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->in(__DIR__)
;

$customRules = [
  '@Symfony' => true,
  'array_syntax' => ['syntax' => 'short'],
  'concat_space' => ['spacing' => 'one'],
  'increment_style' => ['style' => 'post'],
  'declare_strict_types' => true,
  'phpdoc_summary' => false,
];

$psr12Rules = [
  '@PSR2' => true,
  'blank_line_after_opening_tag' => true,
  'braces' => ['allow_single_line_closure' => true],
  'compact_nullable_typehint' => true,
  'concat_space' => ['spacing' => 'one'],
  'declare_equal_normalize' => ['space' => 'none'],
  'function_typehint_space' => true,
  'new_with_braces' => true,
  'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
  'no_empty_statement' => true,
  'no_leading_import_slash' => true,
  'no_leading_namespace_whitespace' => true,
  'no_whitespace_in_blank_line' => true,
  'return_type_declaration' => ['space_before' => 'none'],
  'single_trait_insert_per_statement' => true,
];

$config = new PhpCsFixer\Config();
$config->setRules($customRules + $psr12Rules)
  ->setFinder($finder);

return $config;
