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
 * Legacy system roles to be converted from 3.9.x to 4.0.0.
 */
class SystemRole extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            'registered_perm'  => 'anyone',
            'author_perm'      => 'author',
            'responsible_perm' => 'responsible',
        ];
    }
}
