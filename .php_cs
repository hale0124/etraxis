<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(realpath(__DIR__ . '/src'))
    ->in(realpath(__DIR__ . '/tests'))
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::CONTRIB_LEVEL)
    ->fixers([

        // PSR2_LEVEL
        '-braces',

        // SYMFONY_LEVEL
        '-concat_without_spaces',
        '-phpdoc_params',
        '-phpdoc_separation',
        '-phpdoc_to_comment',
        '-pre_increment',
        '-unalign_double_arrow',
        '-unalign_equals',

        // CONTRIB_LEVEL
        '-ereg_to_preg',
        '-header_comment',
        '-long_array_syntax',
        '-multiline_spaces_before_semicolon',
        '-phpdoc_var_to_type',
        '-strict',
        '-strict_param',
    ])
    ->finder($finder)
;
