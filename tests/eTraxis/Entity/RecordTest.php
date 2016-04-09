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

class RecordTest extends \PHPUnit_Framework_TestCase
{
    /** @var Record */
    private $object;

    protected function setUp()
    {
        $this->object = new Record();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testSubject()
    {
        $expected = 'Subject';
        $this->object->setSubject($expected);
        self::assertEquals($expected, $this->object->getSubject());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        self::assertSame($state, $this->object->getState());
    }

    public function testAuthor()
    {
        $this->object->setAuthor($user = new User());
        self::assertSame($user, $this->object->getAuthor());
    }

    public function testResponsible()
    {
        $this->object->setResponsible($user = new User());
        self::assertSame($user, $this->object->getResponsible());
    }

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        self::assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testChangedAt()
    {
        $expected = time();
        $this->object->setChangedAt($expected);
        self::assertEquals($expected, $this->object->getChangedAt());
    }

    public function testClosedAt()
    {
        $expected = time();
        $this->object->setClosedAt($expected);
        self::assertEquals($expected, $this->object->getClosedAt());
    }

    public function testResumedAt()
    {
        $expected = time();
        $this->object->setResumedAt($expected);
        self::assertEquals($expected, $this->object->getResumedAt());
    }

    public function testHistory()
    {
        self::assertCount(0, $this->object->getHistory());

        $this->object->addEvent($event = new Event());
        self::assertCount(1, $this->object->getHistory());

        $this->object->removeEvent($event);
        self::assertCount(0, $this->object->getHistory());
    }

    public function testWatchers()
    {
        self::assertCount(0, $this->object->getWatchers());

        $this->object->addWatcher($watcher = new Watcher());
        self::assertCount(1, $this->object->getWatchers());

        $this->object->removeWatcher($watcher);
        self::assertCount(0, $this->object->getWatchers());
    }

    public function testChildren()
    {
        self::assertCount(0, $this->object->getChildren());

        $this->object->addChild($child = new Child());
        self::assertCount(1, $this->object->getChildren());

        $this->object->removeChild($child);
        self::assertCount(0, $this->object->getChildren());
    }
}
