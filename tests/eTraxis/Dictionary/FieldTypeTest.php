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

use eTraxis\Entity\Field;

class FieldTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            Field::TYPE_NUMBER,
            Field::TYPE_DECIMAL,
            Field::TYPE_STRING,
            Field::TYPE_TEXT,
            Field::TYPE_CHECKBOX,
            Field::TYPE_LIST,
            Field::TYPE_RECORD,
            Field::TYPE_DATE,
            Field::TYPE_DURATION,
        ];

        self::assertEquals($expected, FieldType::keys());
    }
}
