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

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /** @var Comment */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Comment();
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

    public function testIsConfidential()
    {
        $this->object->setConfidential(false);
        $this->assertFalse($this->object->isConfidential());

        $this->object->setConfidential(true);
        $this->assertTrue($this->object->isConfidential());
    }

    public function testComment()
    {
        $expected = 'Comment';
        $this->object->setComment($expected);
        $this->assertEquals($expected, $this->object->getComment());
    }

    public function testEvent()
    {
        $this->object->setEvent($state = new Event());
        $this->assertSame($state, $this->object->getEvent());
    }
}
