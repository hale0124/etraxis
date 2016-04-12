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
 * Static collection of legacy field types.
 *
 * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
 */
class LegacyFieldType extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            1 => Field::TYPE_NUMBER,
            2 => Field::TYPE_STRING,
            3 => Field::TYPE_TEXT,
            4 => Field::TYPE_CHECKBOX,
            5 => Field::TYPE_LIST,
            6 => Field::TYPE_RECORD,
            7 => Field::TYPE_DATE,
            8 => Field::TYPE_DURATION,
            9 => Field::TYPE_DECIMAL,
        ];
    }
}
