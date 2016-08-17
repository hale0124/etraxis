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

use eTraxis\Security\CurrentUser;
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

    public function testIsAssigned()
    {
        /** @var Record $assigned */
        $assigned = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertTrue($assigned->isAssigned());
        self::assertFalse($this->object->isAssigned());
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

    public function testIsFrozen()
    {
        self::assertTrue($this->object->isFrozen());

        $this->object = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        self::assertFalse($this->object->isFrozen());

        $this->object = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Space Pilot 3000',
        ]);

        self::assertFalse($this->object->isFrozen());
    }

    public function testHistory()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        self::assertCount(4, $record->getHistory());
        self::assertCount(5, $record->getHistory(true));
        self::assertCount(4, $record->getHistory(false));
    }

    public function testWatchers()
    {
        self::assertCount(0, $this->object->getWatchers());

        $this->object->addWatcher($watcher = new Watcher());
        self::assertCount(1, $this->object->getWatchers());

        $this->object->removeWatcher($watcher);
        self::assertCount(0, $this->object->getWatchers());
    }

    public function testGetStates1()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'PHPDoc Standard',
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        self::assertCount(1, $record->getStates($user));
    }

    public function testGetStates2()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Basic Coding Standard',
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        self::assertCount(2, $record->getStates($user));
    }

    public function testGetStates3()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject'  => 'Autoloading Standard',
            'closedAt' => strtotime('2014-10-08 13:04 GMT+13'),
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        self::assertCount(3, $record->getStates($user));
    }

    public function testGetComments()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        self::assertCount(2, $record->getComments());
        self::assertCount(3, $record->getComments(true));
        self::assertCount(2, $record->getComments(false));
    }

    public function testGetAttachments()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject'  => 'Autoloading Standard',
            'closedAt' => null,
        ]);

        self::assertCount(1, $record->getAttachments());
    }
}
