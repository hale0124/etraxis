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

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /** @var Comment */
    private $object;

    protected function setUp()
    {
        $this->object = new Comment();
    }

    public function testId()
    {
        self::assertEquals(null, $this->object->getId());
    }

    public function testEventId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setEventId($expected);
        self::assertEquals($expected, $this->object->getEventId());
    }

    public function testIsPrivate()
    {
        $this->object->setPrivate(false);
        self::assertFalse($this->object->isPrivate());

        $this->object->setPrivate(true);
        self::assertTrue($this->object->isPrivate());
    }

    public function testComment()
    {
        $expected = 'Comment';
        $this->object->setComment($expected);
        self::assertEquals($expected, $this->object->getComment());
    }

    public function testEvent()
    {
        $this->object->setEvent($state = new Event());
        self::assertSame($state, $this->object->getEvent());
    }
}
