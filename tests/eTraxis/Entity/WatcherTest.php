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
        $this->assertNull($this->object->getIssueId());
    }

    public function testWatcherId()
    {
        $this->assertNull($this->object->getWatcherId());
    }

    public function testInitiatorId()
    {
        $this->assertNull($this->object->getInitiatorId());
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
