<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use AltrEgo\AltrEgo;

class FieldValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldValue */
    private $object;

    protected function setUp()
    {
        $this->object = new FieldValue();
    }

    public function testEvent()
    {
        $this->object->setEvent($event = new Event());
        self::assertEquals($event, $this->object->getEvent());
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        self::assertEquals($field, $this->object->getField());
    }

    public function testType()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);
        self::assertNotEquals(1, $object->type);

        $field = new Field();
        $field->setType(Field::TYPE_STRING);
        $this->object->setField($field);
        self::assertEquals(2, $object->type);
    }

    public function testIsCurrent()
    {
        $this->object->setCurrent(false);
        self::assertFalse($this->object->isCurrent());

        $this->object->setCurrent(true);
        self::assertTrue($this->object->isCurrent());
    }

    public function testValueId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setValueId($expected);
        self::assertEquals($expected, $this->object->getValueId());
    }
}
