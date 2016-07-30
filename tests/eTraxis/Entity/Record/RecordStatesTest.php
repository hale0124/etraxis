<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use eTraxis\Entity\Record;
use eTraxis\Security\CurrentUser;
use eTraxis\Tests\TransactionalTestCase;

class RecordStatesTest extends TransactionalTestCase
{
    public function testGetStates1()
    {
        $expected = [
            'Draft',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'PHPDoc Standard',
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        $actual = [];

        foreach ($record->getStates($user) as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetStates2()
    {
        $expected = [
            'Draft',
            'Accepted',
        ];

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Basic Coding Standard',
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        $actual = [];

        foreach ($record->getStates($user) as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetStates3()
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

        $user = new CurrentUser($this->findUser('mwop'));

        $actual = [];

        foreach ($record->getStates($user) as $state) {
            $actual[] = $state->getName();
        }

        self::assertEquals($expected, $actual);
    }

    public function testGetStateFields()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Basic Coding Standard',
        ]);

        $user = new CurrentUser($this->findUser('mwop'));

        $states = $record->getStates($user);

        self::assertCount(2, $states[0]->getFields());
        self::assertCount(0, $states[1]->getFields());

        $fields = $states[0]->getFields();
        self::assertEquals('PSR ID', reset($fields)->getName());
    }

    public function testGetStateFieldsByRestricted()
    {
        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Prizes for the claw crane',
        ]);

        $user   = new CurrentUser($this->findUser('fry'));
        $states = $record->getStates($user);

        self::assertCount(4, $states[0]->getFields());
        self::assertCount(1, $states[1]->getFields());

        $user   = new CurrentUser($this->findUser('zoidberg'));
        $states = $record->getStates($user);

        self::assertCount(3, $states[0]->getFields());
        self::assertCount(0, $states[1]->getFields());
    }

    public function testGetStateFieldsByAuthor()
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

        $states = $record->getStates($user);
        $fields = $states[0]->getFields();

        self::assertCount(3, $fields);

        foreach ($fields as $i => $field) {
            self::assertEquals($expected[$i], $field->getName());
        }
    }

    public function testGetStateFieldsByResponsible()
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

        $states = $record->getStates($user);
        $fields = $states[0]->getFields();

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

        foreach ($record->getStates($user) as $state) {
            foreach ($state->getFields() as $field) {
                self::assertEquals($expected[$field->getName()], $field->getValue());
            }
        }
    }
}
