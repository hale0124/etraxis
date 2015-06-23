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

namespace eTraxis\Model;

class AttachmentTest extends \PHPUnit_Framework_TestCase
{
    /** @var Attachment */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Attachment();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testEventId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setEventId($expected);
        $this->assertEquals($expected, $this->object->getEventId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testType()
    {
        $expected = 'Type';
        $this->object->setType($expected);
        $this->assertEquals($expected, $this->object->getType());
    }

    public function testSize()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setSize($expected);
        $this->assertEquals($expected, $this->object->getSize());
    }

    public function testIsRemoved()
    {
        $this->object->setRemoved(false);
        $this->assertFalse($this->object->isRemoved());

        $this->object->setRemoved(true);
        $this->assertTrue($this->object->isRemoved());
    }

    public function testEvent()
    {
        $this->object->setEvent($state = new Event());
        $this->assertSame($state, $this->object->getEvent());
    }
}
