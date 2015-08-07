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

class StateAssigneeTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateAssignee */
    private $object = null;

    protected function setUp()
    {
        $this->object = new StateAssignee();
    }

    public function testStateId()
    {
        $this->assertNull($this->object->getStateId());
    }

    public function testGroupId()
    {
        $this->assertNull($this->object->getGroupId());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        $this->assertSame($state, $this->object->getState());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        $this->assertSame($group, $this->object->getGroup());
    }
}
