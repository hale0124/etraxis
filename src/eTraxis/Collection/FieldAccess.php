<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Entity\Field;

/**
 * Static collection of field access levels.
 */
class FieldAccess extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            Field::ACCESS_DENIED     => 'access.denied',
            Field::ACCESS_READ_ONLY  => 'access.read_only',
            Field::ACCESS_READ_WRITE => 'access.read_write',
        ];
    }
}
