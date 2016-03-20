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
use eTraxis\Tests\BaseTestCase;

class FieldTypeTest extends BaseTestCase
{
    public function testGetCollection()
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

        $this->assertEquals($expected, array_keys(FieldType::getCollection()));
    }
}
