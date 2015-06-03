<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(realpath(__DIR__ . '/src'))
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([

        // PSR2_LEVEL
        '-braces',

        // SYMFONY_LEVEL
        '-concat_without_spaces',
        '-phpdoc_params',
        '-phpdoc_separation',
        '-phpdoc_to_comment',
        '-unalign_double_arrow',
        '-unalign_equals',

        // CONTRIB_LEVEL
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder)
;