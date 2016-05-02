<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

use Dictionary\StaticDictionary;

/**
 * CSV delimiters.
 */
class CsvDelimiter extends StaticDictionary
{
    const FALLBACK = self::COMMA;

    const TAB           = 1;
    const SPACE         = 2;
    const COMMA         = 3;
    const COLON         = 4;
    const SEMICOLON     = 5;
    const VERTICAL_LINE = 6;

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::TAB           => 'key.tab',
            self::SPACE         => 'key.space',
            self::COMMA         => 'key.comma',
            self::COLON         => 'key.colon',
            self::SEMICOLON     => 'key.semicolon',
            self::VERTICAL_LINE => 'key.vertical_line',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function get($key)
    {
        $delimiters = [
            self::TAB           => "\t",
            self::SPACE         => ' ',
            self::COMMA         => ',',
            self::COLON         => ':',
            self::SEMICOLON     => ';',
            self::VERTICAL_LINE => '|',
        ];

        return $delimiters[$key] ?? $delimiters[static::FALLBACK];
    }
}
