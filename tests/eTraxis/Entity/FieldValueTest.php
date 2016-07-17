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
            'subject' => 'Rebirth',
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
            'token' => md5('6ACV01'),
        ]);

        /** @var TextValue $text_value */
        $text_value = $this->doctrine->getRepository(TextValue::class)->findOneBy([
            'token' => md5('Fry finds his body covered in severe burns but cannot remember why. Professor Farnsworth reveals that the wormhole the Planet Express crew flew through to escape Zapp Brannigan led them back to Earth, where both ships crashed and killed everyone else. Farnsworth uses a birthing machine and resurrects everyone except Leela, who emerges in a supposedly irreversible coma. Devastated, Fry creates a robot replica of Leela with all her memories uploaded into it to continue their newfound relationship. However, the real Leela reawakens from her coma and gets into a fight with the robot Leela over Fry. Fry refuses to shoot either Leela when given the choice and accidentally shoots himself instead, and is revealed to be a robot as well. Farnsworth explains that the real Fry died protecting Leela in the crash and could not be resurrected in the then-incomplete birthing machine, so Leela made a robot replica of him that malfunctioned, killing her and leaving the robot Fry\'s body burned. Suddenly, the real Fry emerges from the birthing machine as it turns out the process was merely delayed for him. The robot Fry and Leela become a couple since they are already in love with each other, as do the real Fry and Leela, and the Planet Express crew celebrate their complete return.'),
        ]);

        /** @var DecimalValue $decimal_value */
        $decimal_value = $this->doctrine->getRepository(DecimalValue::class)->findOneBy([
            'value' => '2.92',
        ]);

        /** @var Record $record */
        $record = $this->doctrine->getRepository(Record::class)->findOneBy([
            'subject' => 'e-Waste',
        ]);

        $expected = [
            'Season'            => $list_item->getValue(),
            'Episode'           => 1,
            'Production code'   => $string_value->getId(),
            'Running time'      => '0:22',
            'Multipart'         => false,
            'Plot'              => $text_value->getId(),
            'Delivery'          => $record->getId(),
            'Original air date' => strtotime('2010-06-24'),
            'U.S. viewers'      => $decimal_value->getId(),
        ];

        foreach ($expected as $field => $value) {
            self::assertEquals($value, $this->object[$field]->getValue());
        }
    }
}
