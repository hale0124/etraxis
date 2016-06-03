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
use eTraxis\Dictionary\EventType;

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

        $expected   = random_int(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
    }

    public function testRecord()
    {
        $this->object->setRecord($record = new Record());
        self::assertEquals($record, $this->object->getRecord());
    }

    public function testUser()
    {
        $this->object->setUser($user = new User());
        self::assertEquals($user, $this->object->getUser());
    }

    public function testType()
    {
        $expected = EventType::RECORD_CREATED;
        $this->object->setType($expected);
        self::assertEquals($expected, $this->object->getType());
    }

    public function testCreatedAt()
    {
        self::assertLessThanOrEqual(1, time() - $this->object->getCreatedAt());
    }
}
