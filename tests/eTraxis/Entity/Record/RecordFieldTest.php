<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use eTraxis\Dictionary\FieldType;

class RecordFieldTest extends \PHPUnit_Framework_TestCase
{
    /** @var RecordField */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new RecordField('Delta', FieldType::NUMBER, 100);
    }

    public function testName()
    {
        $expected = 'Delta';
        self::assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        $expected = FieldType::NUMBER;
        self::assertEquals($expected, $this->object->getType());
    }

    public function testValue()
    {
        $expected = 100;
        self::assertEquals($expected, $this->object->getValue());
    }
}
