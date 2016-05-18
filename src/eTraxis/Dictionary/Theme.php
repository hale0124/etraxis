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
 * Themes.
 */
class Theme extends StaticDictionary
{
    const FALLBACK = self::AZURE;

    const ALLBLACKS = 'allblacks';
    const AZURE     = 'azure';
    const EMERALD   = 'emerald';
    const HUMANITY  = 'humanity';
    const MARS      = 'mars';
    const NEXADA    = 'nexada';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::ALLBLACKS => 'All Blacks',
            self::AZURE     => 'Azure',
            self::EMERALD   => 'Emerald',
            self::HUMANITY  => 'Humanity',
            self::MARS      => 'Mars',
            self::NEXADA    => 'Nexada',
        ];
    }
}
