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

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /** @var Issue */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Issue();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testSubject()
    {
        $expected = 'Subject';
        $this->object->setSubject($expected);
        $this->assertEquals($expected, $this->object->getSubject());
    }

    public function testStateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setStateId($expected);
        $this->assertEquals($expected, $this->object->getStateId());
    }

    public function testAuthorId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setAuthorId($expected);
        $this->assertEquals($expected, $this->object->getAuthorId());
    }

    public function testResponsibleId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setResponsibleId($expected);
        $this->assertEquals($expected, $this->object->getResponsibleId());
    }

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        $this->assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testChangedAt()
    {
        $expected = time();
        $this->object->setChangedAt($expected);
        $this->assertEquals($expected, $this->object->getChangedAt());
    }

    public function testClosedAt()
    {
        $expected = time();
        $this->object->setClosedAt($expected);
        $this->assertEquals($expected, $this->object->getClosedAt());
    }

    public function testResumedAt()
    {
        $expected = time();
        $this->object->setResumedAt($expected);
        $this->assertEquals($expected, $this->object->getResumedAt());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        $this->assertSame($state, $this->object->getState());
    }

    public function testAuthor()
    {
        $this->object->setAuthor($user = new User());
        $this->assertSame($user, $this->object->getAuthor());
    }

    public function testResponsible()
    {
        $this->object->setResponsible($user = new User());
        $this->assertSame($user, $this->object->getResponsible());
    }

    public function testHistory()
    {
        $this->assertCount(0, $this->object->getHistory());

        $this->object->addEvent($event = new Event());
        $this->assertCount(1, $this->object->getHistory());

        $this->object->removeEvent($event);
        $this->assertCount(0, $this->object->getHistory());
    }

    public function testWatchers()
    {
        $this->assertCount(0, $this->object->getWatchers());

        $this->object->addWatcher($watcher = new Watcher());
        $this->assertCount(1, $this->object->getWatchers());

        $this->object->removeWatcher($watcher);
        $this->assertCount(0, $this->object->getWatchers());
    }

    public function testChildren()
    {
        $this->assertCount(0, $this->object->getChildren());

        $this->object->addChild($child = new Child());
        $this->assertCount(1, $this->object->getChildren());

        $this->object->removeChild($child);
        $this->assertCount(0, $this->object->getChildren());
    }
}
