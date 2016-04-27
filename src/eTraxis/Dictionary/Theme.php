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
    const FALLBACK = 'azure';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            'allblacks' => 'All Blacks',
            'azure'     => 'Azure',
            'emerald'   => 'Emerald',
            'humanity'  => 'Humanity',
            'mars'      => 'Mars',
            'nexada'    => 'Nexada',
        ];
    }
}
