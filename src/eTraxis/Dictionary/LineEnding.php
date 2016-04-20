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
 * Static collection of line ending.
 */
class LineEnding extends StaticDictionary
{
    const WINDOWS   = 1;
    const UNIX      = 2;
    const MACINTOSH = 3;

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::WINDOWS   => 'Windows',
            self::UNIX      => 'Unix',
            self::MACINTOSH => 'Macintosh',
        ];
    }

    /**
     * Returns specified line ending.
     *
     * @param   int $line_ending Line ending ID.
     *
     * @return  string
     */
    public static function getLineEnding($line_ending)
    {
        $line_endings = [
            self::WINDOWS   => "\r\n",
            self::UNIX      => "\n",
            self::MACINTOSH => "\r",
        ];

        return array_key_exists($line_ending, $line_endings)
            ? $line_endings[$line_ending]
            : null;
    }
}
