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

class NotificationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Notification */
    private $object;

    protected function setUp()
    {
        $this->object = new Notification();
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

    public function testCarbonCopy()
    {
        $expected = 'CarbonCopy@example.com';
        $this->object->setCarbonCopy($expected);
        self::assertEquals($expected, $this->object->getCarbonCopy());
    }

    public function testIsActivated()
    {
        $this->object->setActivated(false);
        self::assertFalse($this->object->isActivated());

        $this->object->setActivated(true);
        self::assertTrue($this->object->isActivated());
    }

    public function testType()
    {
        $expected = Notification::TYPE_ALL;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
    }

    public function testEvents()
    {
        $expected = Notification::NOTIFY_RECORD_CREATED | Notification::NOTIFY_COMMENT_ADDED;
        $this->object->setEvents($expected);
        self::assertEquals($expected, $this->object->getEvents());
    }

    public function testParameter()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setParameter($expected);
        self::assertEquals($expected, $this->object->getParameter());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertSame($user, $this->object->getUser());
    }
}
