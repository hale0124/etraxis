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
 * System roles.
 */
class SystemRole extends StaticDictionary
{
    const AUTHOR      = -1;  // creator of the record
    const RESPONSIBLE = -2;  // user assigned on the record
    const REGISTERED  = -3;  // any authenticated user

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::AUTHOR      => 'role.author',
            self::RESPONSIBLE => 'role.responsible',
            self::REGISTERED  => 'role.registered',
        ];
    }
}
