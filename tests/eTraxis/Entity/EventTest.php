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

class EventTest extends \PHPUnit_Framework_TestCase
{
    /** @var Event */
    private $object;

    protected function setUp()
    {
        $this->object = new Event();
    }

    public function testId()
    {
        self::assertEquals(null, $this->object->getId());
    }

    public function testRecordId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setRecordId($expected);
        self::assertEquals($expected, $this->object->getRecordId());
    }

    public function testUserId()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setUserId($expected);
        self::assertEquals($expected, $this->object->getUserId());
    }

    public function testType()
    {
        $expected = Event::RECORD_CREATED;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
    }

    public function testCreatedAt()
    {
        $expected = time();
        $this->object->setCreatedAt($expected);
        self::assertEquals($expected, $this->object->getCreatedAt());
    }

    public function testParameter()
    {
        $expected = mt_rand(1, PHP_INT_MAX);
        $this->object->setParameter($expected);
        self::assertEquals($expected, $this->object->getParameter());
    }

    public function testRecord()
    {
        $this->object->setRecord($record = new Record());
        self::assertSame($record, $this->object->getRecord());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertSame($user, $this->object->getUser());
    }
}
