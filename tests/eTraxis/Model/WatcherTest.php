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

class WatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Watcher */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Watcher();
    }

    public function testIssueId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setIssueId($expected);
        $this->assertEquals($expected, $this->object->getIssueId());
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

    public function testIssue()
    {
        $this->object->setIssue($issue = new Issue());
        $this->assertSame($issue, $this->object->getIssue());
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
