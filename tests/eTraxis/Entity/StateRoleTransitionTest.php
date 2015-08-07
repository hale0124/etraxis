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

use eTraxis\Collection\SystemRole;

class StateRoleTransitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateRoleTransition */
    private $object = null;

    protected function setUp()
    {
        $this->object = new StateRoleTransition();
    }

    public function testFromStateId()
    {
        $this->assertNull($this->object->getFromStateId());
    }

    public function testToStateId()
    {
        $this->assertNull($this->object->getToStateId());
    }

    public function testRole()
    {
        $expected = SystemRole::AUTHOR;
        $this->object->setRole($expected);
        $this->assertEquals($expected, $this->object->getRole());
    }

    public function testFromState()
    {
        $this->object->setFromState($state = new State());
        $this->assertSame($state, $this->object->getFromState());
    }

    public function testToState()
    {
        $this->object->setToState($state = new State());
        $this->assertSame($state, $this->object->getToState());
    }
}
