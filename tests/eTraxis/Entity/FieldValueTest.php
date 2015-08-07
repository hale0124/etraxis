<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

class FieldValueTest extends \PHPUnit_Framework_TestCase
{
    /** @var FieldValue */
    private $object = null;

    protected function setUp()
    {
        $this->object = new FieldValue();
    }

    public function testEventId()
    {
        $this->assertNull($this->object->getEventId());
    }

    public function testFieldId()
    {
        $this->assertNull($this->object->getFieldId());
    }

    public function testValueId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setValueId($expected);
        $this->assertEquals($expected, $this->object->getValueId());
    }

    public function testType()
    {
        $expected = Field::TYPE_NUMBER;
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testIsCurrent()
    {
        $this->object->setCurrent(false);
        $this->assertFalse($this->object->isCurrent());

        $this->object->setCurrent(true);
        $this->assertTrue($this->object->isCurrent());
    }

    public function testEvent()
    {
        $this->object->setEvent($event = new Event());
        $this->assertSame($event, $this->object->getEvent());
    }

    public function testField()
    {
        $this->object->setField($field = new Field());
        $this->assertSame($field, $this->object->getField());
    }
}
