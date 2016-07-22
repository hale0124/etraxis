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

class FieldValueTest extends TransactionalTestCase
{
    /** @var FieldValue[] */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'Attack of the Killer App',
        ]);

        $query = $manager->createQueryBuilder()
            ->select('field_value')
            ->addSelect('event')
            ->addSelect('field')
            ->from(FieldValue::class, 'field_value')
            ->innerJoin('field_value.event', 'event')
            ->innerJoin('field_value.field', 'field')
            ->where('event.record = :record')
            ->setParameter('record', $record)
        ;

        /** @var FieldValue $field_value */
        foreach ($query->getQuery()->getResult() as $field_value) {
            $this->object[$field_value->getField()->getName()] = $field_value;
        }
    }

    public function testEvent()
    {
        self::assertEquals(EventType::RECORD_CREATED, $this->object['Season']->getEvent()->getType());
        self::assertEquals(EventType::STATE_CHANGED, $this->object['Original air date']->getEvent()->getType());
    }

    public function testField()
    {
        self::assertEquals('Season', $this->object['Season']->getField()->getName());
    }

    public function testIsCurrent()
    {
        self::assertTrue($this->object['Season']->isCurrent());
    }

    public function testValue()
    {
        /** @var ListItem $list_item */
        $list_item = $this->doctrine->getRepository(ListItem::class)->findOneBy([
            'text' => 'Season 6',
        ]);

        /** @var StringValue $string_value */
        $string_value = $this->doctrine->getRepository(StringValue::class)->findOneBy([
            'token' => md5('6ACV03'),
        ]);

        /** @var TextValue $text_value */
        $text_value = $this->doctrine->getRepository(TextValue::class)->findOneBy([
            'token' => md5('Everyone in New New York buys the latest, state of the art eyePhone, a device developed by Mom which is implanted in a person\'s eye that allows users to record videos and post them online. Fry and Bender challenge each other to see who can gain one million followers on their Twitcher accounts, with the loser having to dive into a pool of goat vomit and diarrhea. With Bender in the lead, Fry resorts to posting an embarrassing video of Leela revealing she has a singing boil on her rear named Susan, gaining him enough followers to end the bet with a tie. However, Leela is humiliated, so Fry posts a video of himself diving into the pool out of guilt, which everyone watches and causes them to forget about the video of Leela. Fry and Leela reconcile, completely unaware that Mom has infected all of Fry and Bender\'s followers with a virus that turns them into mindless zombies to make them buy more eyePhones.'),
        ]);

        /** @var DecimalValue $decimal_value */
        $decimal_value = $this->doctrine->getRepository(DecimalValue::class)->findOneBy([
            'value' => '2.16',
        ]);

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $expected = [
            'Season'            => $list_item->getValue(),
            'Episode'           => 3,
            'Production code'   => $string_value->getId(),
            'Running time'      => '0:22',
            'Multipart'         => false,
            'Plot'              => $text_value->getId(),
            'Delivery'          => $record->getId(),
            'Original air date' => strtotime('2010-07-01'),
            'U.S. viewers'      => $decimal_value->getId(),
        ];

        foreach ($expected as $field => $value) {
            self::assertEquals($value, $this->object[$field]->getValue());
        }
    }
}
