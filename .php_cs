<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(realpath(__DIR__ . '/src'))
    ->in(realpath(__DIR__ . '/tests'))
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([

        // PSR2_LEVEL
        '-braces',

        // SYMFONY_LEVEL
        '-concat_without_spaces',
        '-empty_return',
        '-no_empty_comment',
        '-phpdoc_annotation_without_dot',
        '-phpdoc_params',
        '-phpdoc_separation',
        '-phpdoc_to_comment',
        '-pre_increment',
        '-unalign_double_arrow',
        '-unalign_equals',

        // CONTRIB_LEVEL
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'no_blank_lines_before_namespace',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder)
;
