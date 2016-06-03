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
 * Field types.
 */
class FieldType extends StaticDictionary
{
    const NUMBER   = 'number';
    const DECIMAL  = 'decimal';
    const STRING   = 'string';
    const TEXT     = 'text';
    const CHECKBOX = 'checkbox';
    const LIST     = 'list';
    const RECORD   = 'record';
    const DATE     = 'date';
    const DURATION = 'duration';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::NUMBER   => 'field.type.number',
            self::DECIMAL  => 'field.type.decimal',
            self::STRING   => 'field.type.string',
            self::TEXT     => 'field.type.text',
            self::CHECKBOX => 'field.type.checkbox',
            self::LIST     => 'field.type.list',
            self::RECORD   => 'field.type.record',
            self::DATE     => 'field.type.date',
            self::DURATION => 'field.type.duration',
        ];
    }
}
