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
use eTraxis\Entity\Field;

/**
 * Field permissions.
 */
class FieldPermission extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            Field::ACCESS_DENIED     => 'field.permissions.none',
            Field::ACCESS_READ_ONLY  => 'field.permissions.read_only',
            Field::ACCESS_READ_WRITE => 'field.permissions.read_write',
        ];
    }
}
