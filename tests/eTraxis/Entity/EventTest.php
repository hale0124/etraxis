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

use eTraxis\Dictionary\EventType;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class EventTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var Event */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);

        $this->object = new Event($record, $this->findUser('hubert'), EventType::PUBLIC_COMMENT);
    }

    public function testId()
    {
        $expected = random_int(1, PHP_INT_MAX);
        $this->setProperty($this->object, 'id', $expected);
        self::assertEquals($expected, $this->object->getId());
    }

    public function testRecord()
    {
        $expected = 'Prizes for the claw crane';
        self::assertEquals($expected, $this->object->getRecord()->getSubject());
    }

    public function testUser()
    {
        $expected = 'Hubert J. Farnsworth';
        self::assertEquals($expected, $this->object->getUser()->getFullname());
    }

    public function testType()
    {
        $expected = EventType::PUBLIC_COMMENT;
        self::assertEquals($expected, $this->object->getType());
    }

    public function testCreatedAt()
    {
        self::assertLessThanOrEqual(1, time() - $this->object->getCreatedAt());
    }
}
