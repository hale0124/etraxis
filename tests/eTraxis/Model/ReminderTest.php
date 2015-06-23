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

class ReminderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Reminder */
    private $object = null;

    protected function setUp()
    {
        $this->object = new Reminder();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testUserId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        $this->assertEquals($expected, $this->object->getUserId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        $this->assertEquals($expected, $this->object->getName());
    }

    public function testSubject()
    {
        $expected = 'Email subject';
        $this->object->setSubject($expected);
        $this->assertEquals($expected, $this->object->getSubject());
    }

    public function testStateId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setStateId($expected);
        $this->assertEquals($expected, $this->object->getStateId());
    }

    public function testGroupId()
    {
        $expected = rand(1, PHP_INT_MAX);
        $this->object->setGroupId($expected);
        $this->assertEquals($expected, $this->object->getGroupId());
    }

    public function testRole()
    {
        $expected = SystemRole::RESPONSIBLE;
        $this->object->setRole($expected);
        $this->assertEquals($expected, $this->object->getRole());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        $this->assertSame($user, $this->object->getUser());
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
