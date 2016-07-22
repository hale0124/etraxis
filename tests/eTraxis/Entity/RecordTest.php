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

    public function testGetAllStates1()
    {
        $expected = [
            'Draft',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'PHPDoc Standard',
        ]);

        $actual = [];

        foreach ($record->getAllStates() as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetAllStates2()
    {
        $expected = [
            'Draft',
            'Accepted',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Basic Coding Standard',
        ]);

        $actual = [];

        foreach ($record->getAllStates() as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetAllStates3()
    {
        $expected = [
            'Draft',
            'Accepted',
            'Deprecated',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject'  => 'Autoloading Standard',
            'closedAt' => strtotime('2014-10-08 13:04 GMT+13'),
        ]);

        $actual = [];

        foreach ($record->getAllStates() as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetFieldsByState()
    {
        $user = new CurrentUser($this->findUser('mwop'));

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Basic Coding Standard',
        ]);

        $states = $record->getAllStates();

        self::assertCount(2, $record->getFieldsByState($states[0], $user));
        self::assertCount(0, $record->getFieldsByState($states[1], $user));

        $fields = $record->getFieldsByState($states[0], $user);
        self::assertEquals('PSR ID', reset($fields)->getName());
    }

    public function testGetFieldsByStateRestricted()
    {
        $states = $this->object->getAllStates();

        $user = new CurrentUser($this->findUser('fry'));

        self::assertCount(4, $this->object->getFieldsByState($states[0], $user));
        self::assertCount(1, $this->object->getFieldsByState($states[1], $user));

        $user = new CurrentUser($this->findUser('zoidberg'));

        self::assertCount(3, $this->object->getFieldsByState($states[0], $user));
        self::assertCount(0, $this->object->getFieldsByState($states[1], $user));
    }

    public function testGetFieldsByStateAuthor()
    {
        $expected = [
            'Crew',
            'Delivery at',
            'Notes',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'A statue commemorating the loss of the first Planet Express crew',
        ]);

        $user = new CurrentUser($this->findUser('pmjones'));

        $states = $record->getAllStates();
        $fields = $record->getFieldsByState(reset($states), $user);

        self::assertCount(3, $fields);

        foreach ($fields as $i => $field) {
            self::assertEquals($expected[$i], $field->getName());
        }
    }

    public function testGetFieldsByStateResponsible()
    {
        $expected = [
            'Crew',
            'Delivery to',
            'Notes',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => '200 feet of hanging rope for the hanging of multiheaded monster.',
        ]);

        $user = new CurrentUser($this->findUser('artem'));

        $states = $record->getAllStates();
        $fields = $record->getFieldsByState(reset($states), $user);

        self::assertCount(3, $fields);

        foreach ($fields as $i => $field) {
            self::assertEquals($expected[$i], $field->getName());
        }
    }

    public function testGetFieldValue()
    {
        $user = new CurrentUser($this->findUser('mwop'));

        /** @var Record $delivery */
        $delivery = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $expected = [
            'Season'            => 'Season 6',
            'Episode'           => 3,
            'Production code'   => '6ACV03',
            'Running time'      => '0:22',
            'Multipart'         => false,
            'Plot'              => 'Everyone in New New York buys the latest, state of the art eyePhone, a device developed by Mom which is implanted in a person\'s eye that allows users to record videos and post them online. Fry and Bender challenge each other to see who can gain one million followers on their Twitcher accounts, with the loser having to dive into a pool of goat vomit and diarrhea. With Bender in the lead, Fry resorts to posting an embarrassing video of Leela revealing she has a singing boil on her rear named Susan, gaining him enough followers to end the bet with a tie. However, Leela is humiliated, so Fry posts a video of himself diving into the pool out of guilt, which everyone watches and causes them to forget about the video of Leela. Fry and Leela reconcile, completely unaware that Mom has infected all of Fry and Bender\'s followers with a virus that turns them into mindless zombies to make them buy more eyePhones.',
            'Delivery'          => $delivery->getId(),
            'Original air date' => strtotime('2010-07-01'),
            'U.S. viewers'      => '2.16',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Attack of the Killer App',
        ]);

        foreach ($record->getAllStates() as $state) {
            foreach ($state->getFields() as $field) {
                self::assertEquals($expected[$field->getName()], $record->getFieldValue($field, $user));
            }
        }
    }

    public function testGetFieldValueRestricted()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Ceremonial oversized Scissors',
        ]);

        $fields = $record->getState()->getFields();
        $field  = reset($fields);

        self::assertNotNull($record->getFieldValue($field, new CurrentUser($this->findUser('fry'))));
        self::assertNull($record->getFieldValue($field, new CurrentUser($this->findUser('zoidberg'))));
    }
}
