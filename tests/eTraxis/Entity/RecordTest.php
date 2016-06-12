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

use eTraxis\Tests\TransactionalTestCase;

class RecordTest extends TransactionalTestCase
{
    /** @var Record */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);
    }

    public function testId()
    {
        $record = new Record($this->object->getAuthor(), $this->object->getTemplate());
        self::assertNull($record->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testRecordId()
    {
        $expected = sprintf('PE-%d', $this->object->getId());
        self::assertEquals($expected, $this->object->getRecordId());
    }

    public function testSubject()
    {
        $expected = 'Subject';
        $this->object->setSubject($expected);
        self::assertEquals($expected, $this->object->getSubject());
    }

    public function testProject()
    {
        self::assertEquals('Planet Express', $this->object->getProject()->getName());
    }

    public function testTemplate()
    {
        self::assertEquals('Delivery', $this->object->getTemplate()->getName());
    }

    public function testState()
    {
        self::assertEquals('Delivered', $this->object->getState()->getName());
    }

    public function testAuthor()
    {
        self::assertEquals('Hubert J. Farnsworth', $this->object->getAuthor()->getFullname());
    }

    public function testResponsible()
    {
        self::assertNull($this->object->getResponsible());
    }

    public function testCreatedAt()
    {
        self::assertEquals('1999-04-04', date('Y-m-d', $this->object->getCreatedAt()));
    }

    public function testChangedAt()
    {
        self::assertEquals('1999-04-04', date('Y-m-d', $this->object->getChangedAt()));
    }

    public function testClosedAt()
    {
        self::assertEquals('1999-04-04', date('Y-m-d', $this->object->getClosedAt()));
    }

    public function testAge()
    {
        self::assertEquals(1, $this->object->getAge());
    }

    public function testIsOverdue()
    {
        /** @var Record $opened */
        $opened = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertFalse($opened->isOverdue());

        $opened->getTemplate()->setCriticalAge(1);
        self::assertTrue($opened->isOverdue());
    }

    public function testIsClosed()
    {
        self::assertTrue($this->object->isClosed());

        $this->object = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'PHPDoc Standard',
        ]);

        self::assertFalse($this->object->isClosed());
    }

    public function testIsPostponed()
    {
        self::assertFalse($this->object->isPostponed());

        $this->object = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'PHPDoc Standard',
        ]);

        self::assertTrue($this->object->isPostponed());
    }

    public function testHistory()
    {
        self::assertCount(3, $this->object->getHistory());
    }

    public function testWatchers()
    {
        self::assertCount(0, $this->object->getWatchers());

        $this->object->addWatcher($watcher = new Watcher());
        self::assertCount(1, $this->object->getWatchers());

        $this->object->removeWatcher($watcher);
        self::assertCount(0, $this->object->getWatchers());
    }
}
