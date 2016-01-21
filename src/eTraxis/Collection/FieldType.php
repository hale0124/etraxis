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
 * Static collection of field types.
 */
class FieldType extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            Field::TYPE_NUMBER   => 'type.number',
            Field::TYPE_STRING   => 'type.string',
            Field::TYPE_TEXT     => 'type.text',
            Field::TYPE_CHECKBOX => 'type.checkbox',
            Field::TYPE_LIST     => 'type.list',
            Field::TYPE_ISSUE    => 'type.issue',
            Field::TYPE_DATE     => 'type.date',
            Field::TYPE_DURATION => 'type.duration',
            Field::TYPE_DECIMAL  => 'type.decimal',
        ];
    }
}
