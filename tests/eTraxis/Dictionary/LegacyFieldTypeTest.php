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

class LegacyFieldTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDictionary()
    {
        self::assertEquals(FieldType::NUMBER, Legacy\FieldType::get(1));
        self::assertEquals(FieldType::STRING, Legacy\FieldType::get(2));
        self::assertEquals(FieldType::TEXT, Legacy\FieldType::get(3));
        self::assertEquals(FieldType::CHECKBOX, Legacy\FieldType::get(4));
        self::assertEquals(FieldType::LIST, Legacy\FieldType::get(5));
        self::assertEquals(FieldType::RECORD, Legacy\FieldType::get(6));
        self::assertEquals(FieldType::DATE, Legacy\FieldType::get(7));
        self::assertEquals(FieldType::DURATION, Legacy\FieldType::get(8));
        self::assertEquals(FieldType::DECIMAL, Legacy\FieldType::get(9));
    }
}
