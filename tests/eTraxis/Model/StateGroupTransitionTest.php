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

class StateGroupTransitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var StateGroupTransition */
    private $object = null;

    protected function setUp()
    {
        $this->object = new StateGroupTransition();
    }

    public function testFromStateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setFromStateId($expected);
        $this->assertEquals($expected, $this->object->getFromStateId());
    }

    public function testToStateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setToStateId($expected);
        $this->assertEquals($expected, $this->object->getToStateId());
    }

    public function testGroupId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setGroupId($expected);
        $this->assertEquals($expected, $this->object->getGroupId());
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

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        $this->assertSame($group, $this->object->getGroup());
    }
}
