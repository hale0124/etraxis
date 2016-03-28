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

use eTraxis\Collection\SystemRole;

class ReminderTest extends \PHPUnit_Framework_TestCase
{
    /** @var Reminder */
    private $object;

    protected function setUp()
    {
        $this->object = new Reminder();
    }

    public function testId()
    {
        self::assertEquals(null, $this->object->getId());
    }

    public function testUserId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        self::assertEquals($expected, $this->object->getUserId());
    }

    public function testName()
    {
        $expected = 'Name';
        $this->object->setName($expected);
        self::assertEquals($expected, $this->object->getName());
    }

    public function testSubject()
    {
        $expected = 'Email subject';
        $this->object->setSubject($expected);
        self::assertEquals($expected, $this->object->getSubject());
    }

    public function testStateId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setStateId($expected);
        self::assertEquals($expected, $this->object->getStateId());
    }

    public function testGroupId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setGroupId($expected);
        self::assertEquals($expected, $this->object->getGroupId());
    }

    public function testRole()
    {
        $expected = SystemRole::RESPONSIBLE;
        $this->object->setRole($expected);
        self::assertEquals($expected, $this->object->getRole());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertSame($user, $this->object->getUser());
    }

    public function testState()
    {
        $this->object->setState($state = new State());
        self::assertSame($state, $this->object->getState());
    }

    public function testGroup()
    {
        $this->object->setGroup($group = new Group());
        self::assertSame($group, $this->object->getGroup());
    }
}
