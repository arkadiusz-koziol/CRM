<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'app')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'config')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'database')
    ->in(__DIR__ . DIRECTORY_SEPARATOR . 'routes');

return (new PhpCsFixer\Config())
    ->setRules(
        [
            '@PSR2' => true,
            'align_multiline_comment' => true,
            'array_indentation' => true,
            'array_syntax' => ['syntax' => 'short'],
            'blank_line_after_opening_tag' => true,
            'cast_spaces' => true,
            'compact_nullable_type_declaration' => true,
            'concat_space' => ['spacing' => 'one'],
            'explicit_indirect_variable' => true,
            'type_declaration_spaces' => true,
            'heredoc_to_nowdoc' => true,
            'include' => true,
            'linebreak_after_opening_tag' => true,
            'lowercase_cast' => true,
            'lowercase_static_reference' => true,
            'magic_constant_casing' => true,
            'magic_method_casing' => true,
            'method_chaining_indentation' => true,
            'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
            'native_function_casing' => true,
            'native_type_declaration_casing' => true,
            'no_alternative_syntax' => true,
            'no_blank_lines_after_class_opening' => true,
            'no_empty_comment' => true,
            'no_empty_phpdoc' => true,
            'no_empty_statement' => true,
            'no_extra_blank_lines' => true,
            'no_leading_namespace_whitespace' => true,
            'no_multiline_whitespace_around_double_arrow' => true,
            'no_short_bool_cast' => true,
            'no_singleline_whitespace_before_semicolons' => true,
            'no_spaces_around_offset' => true,
            'no_trailing_comma_in_singleline' => true,
            'no_unneeded_braces' => true,
            'no_unused_imports' => true,
            'no_useless_else' => true,
            'no_useless_return' => true,
            'no_whitespace_before_comma_in_array' => true,
            'no_whitespace_in_blank_line' => true,
            'normalize_index_brace' => true,
            'object_operator_without_whitespace' => true,
            'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
            'phpdoc_no_useless_inheritdoc' => true,
            'phpdoc_order' => true,
            'phpdoc_scalar' => true,
            'phpdoc_separation' => true,
            'phpdoc_single_line_var_spacing' => true,
            'phpdoc_trim' => true,
            'phpdoc_types' => true,
            'return_type_declaration' => true,
            'semicolon_after_instruction' => true,
            'short_scalar_cast' => true,
            'blank_lines_before_namespace' => true,
            'single_quote' => true,
            'single_trait_insert_per_statement' => true,
            'space_after_semicolon' => true,
            'ternary_operator_spaces' => true,
            'trailing_comma_in_multiline' => true,
            'trim_array_spaces' => true,
            'unary_operator_spaces' => true,
            'visibility_required' => ['elements' => [
                'property',
                'method',
                'const',
            ]],
            'whitespace_after_comma_in_array' => true,
        ]
    )
    ->setFinder($finder)
    ->setRiskyAllowed(false);
