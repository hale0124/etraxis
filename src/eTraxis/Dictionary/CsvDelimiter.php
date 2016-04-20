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
 * Static collection of CSV delimiters.
 */
class CsvDelimiter extends StaticDictionary
{
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
     * Returns specified delimiter.
     *
     * @param   int $delimiter Delimiter ID.
     *
     * @return  string
     */
    public static function getDelimiter($delimiter)
    {
        $delimiters = [
            self::TAB           => "\t",
            self::SPACE         => ' ',
            self::COMMA         => ',',
            self::COLON         => ':',
            self::SEMICOLON     => ';',
            self::VERTICAL_LINE => '|',
        ];

        return array_key_exists($delimiter, $delimiters)
            ? $delimiters[$delimiter]
            : null;
    }
}
