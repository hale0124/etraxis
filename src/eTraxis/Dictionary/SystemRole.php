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
    const ANYONE      = 'anyone';       // any authenticated user
    const AUTHOR      = 'author';       // creator of the record
    const RESPONSIBLE = 'responsible';  // user assigned to the record

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::ANYONE      => 'role.any',
            self::AUTHOR      => 'role.author',
            self::RESPONSIBLE => 'role.responsible',
        ];
    }
}
