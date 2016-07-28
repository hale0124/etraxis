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
 * Timezones.
 */
class Timezone extends StaticDictionary
{
    const FALLBACK = 'UTC';

    /**
     * {@inheritdoc}
     */
    protected static function dictionary()
    {
        $timezones = timezone_identifiers_list();

        return array_combine($timezones, $timezones);
    }
}
