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

class WatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Watcher */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Watcher();
    }

    public function testRecordId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setRecordId($expected);
        $this->assertEquals($expected, $this->object->getRecordId());
    }

    public function testWatcherId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setWatcherId($expected);
        $this->assertEquals($expected, $this->object->getWatcherId());
    }

    public function testInitiatorId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setInitiatorId($expected);
        $this->assertEquals($expected, $this->object->getInitiatorId());
    }

    public function testRecord()
    {
        $this->object->setRecord($record = new Record());
        $this->assertSame($record, $this->object->getRecord());
    }

    public function testWatcher()
    {
        $this->object->setWatcher($user = new User());
        $this->assertSame($user, $this->object->getWatcher());
    }

    public function testInitiator()
    {
        $this->object->setInitiator($user = new User());
        $this->assertSame($user, $this->object->getInitiator());
    }
}
