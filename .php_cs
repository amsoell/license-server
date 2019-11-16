<?php

return PhpCsFixer\Config::create()
->setRules([
    '@Symfony'     => true,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'no_spaces_inside_parenthesis' => false,
    'trim_array_spaces'            => false,
    'not_operator_with_space'      => true,
    'phpdoc_no_empty_return'       => false,
    'phpdoc_separation'            => false,
    'declare_equal_normalize'      => true,
    'binary_operator_spaces'       => [
        'align_double_arrow' => true,
        'align_equals'       => false,
    ],
    'single_quote'    => false,
    'increment_style' => [
        'style' => 'post',
    ],
    'yoda_style' => [
        'equal'            => false,
        'identical'        => false,
        'less_and_greater' => false,
    ],
    'concat_space' => [
        'spacing' => 'one',
    ],
]);
