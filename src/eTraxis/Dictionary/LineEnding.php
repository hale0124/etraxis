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
    const FALLBACK = self::WINDOWS;

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
     * {@inheritdoc}
     */
    public static function get($key)
    {
        $line_endings = [
            self::WINDOWS   => "\r\n",
            self::UNIX      => "\n",
            self::MACINTOSH => "\r",
        ];

        return array_key_exists($key, $line_endings) ? $line_endings[$key] : $line_endings[static::FALLBACK];
    }
}
