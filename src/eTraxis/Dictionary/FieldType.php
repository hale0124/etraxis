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
 * Static collection of field types.
 */
class FieldType extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            Field::TYPE_NUMBER   => 'field.type.number',
            Field::TYPE_DECIMAL  => 'field.type.decimal',
            Field::TYPE_STRING   => 'field.type.string',
            Field::TYPE_TEXT     => 'field.type.text',
            Field::TYPE_CHECKBOX => 'field.type.checkbox',
            Field::TYPE_LIST     => 'field.type.list',
            Field::TYPE_RECORD   => 'field.type.record',
            Field::TYPE_DATE     => 'field.type.date',
            Field::TYPE_DURATION => 'field.type.duration',
        ];
    }
}
