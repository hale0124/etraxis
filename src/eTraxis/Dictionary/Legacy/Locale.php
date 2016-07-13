<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary\Legacy;

use Dictionary\StaticDictionary;

/**
 * Legacy locales to be converted from 3.9.x to 4.0.0.
 */
class Locale extends StaticDictionary
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
