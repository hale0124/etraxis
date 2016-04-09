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

use AltrEgo\AltrEgo;

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
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
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
}
