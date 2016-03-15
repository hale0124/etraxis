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

class ChangeTest extends \PHPUnit_Framework_TestCase
{
    /** @var Change */
    private $object;

    protected function setUp()
    {
        $this->object = new Change();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testEventId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setEventId($expected);
        $this->assertEquals($expected, $this->object->getEventId());
    }

    public function testFieldId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setFieldId($expected);
        $this->assertEquals($expected, $this->object->getFieldId());
    }

    public function testOldValueId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setOldValueId($expected);
        $this->assertEquals($expected, $this->object->getOldValueId());
    }

    public function testNewValueId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setNewValueId($expected);
        $this->assertEquals($expected, $this->object->getNewValueId());
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
