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
 * Static collection of legacy locales.
 *
 * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
 */
class LegacyLocale extends StaticDictionary
{
    const FALLBACK = 1000;

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            1000 => 'en_US',
            1001 => 'en_GB',
            1002 => 'en_CA',
            1003 => 'en_AU',
            1004 => 'en_NZ',
            1010 => 'fr',
            1020 => 'de',
            1030 => 'it',
            1040 => 'es',
            1080 => 'pt_BR',
            1090 => 'nl',
            2020 => 'sv',
            2050 => 'lv',
            3000 => 'ru',
            3030 => 'pl',
            3040 => 'cs',
            3060 => 'hu',
            3130 => 'bg',
            3140 => 'ro',
            5000 => 'ja',
            6000 => 'tr',
        ];
    }
}
