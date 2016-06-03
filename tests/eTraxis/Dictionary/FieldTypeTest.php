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

class FieldTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        $expected = [
            FieldType::NUMBER,
            FieldType::DECIMAL,
            FieldType::STRING,
            FieldType::TEXT,
            FieldType::CHECKBOX,
            FieldType::LIST,
            FieldType::RECORD,
            FieldType::DATE,
            FieldType::DURATION,
        ];

        self::assertEquals($expected, FieldType::keys());
    }
}
