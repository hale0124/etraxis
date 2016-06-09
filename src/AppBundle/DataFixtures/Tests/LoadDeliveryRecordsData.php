<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use AppBundle\DataFixtures\AltrEgoTrait;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Dictionary\EventType;
use eTraxis\Entity\Event;
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Entity\StringValue;
use eTraxis\Entity\TextValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadDeliveryRecordsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use AltrEgoTrait;

    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 7;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $records = [
            '1ACV01' => [
                'subject'     => 'Prizes for the claw crane',
                'responsible' => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Sal',
                'delivery_at' => 'Luna Park, Moon',
                'notes'       => null,
                'date'        => '1999-04-04',
                'notes2'      => null,
            ],
            '1ACV02' => [
                'subject'     => 'Lug nuts',
                'responsible' => 'user:bender',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robots of Chapek 9',
                'delivery_at' => 'Chapek 9',
                'notes'       => 'Only Bender goes on the planet, because humans would be killed.',
                'date'        => '1999-04-20',
                'notes2'      => null,
            ],
            '1ACV03' => [
                'subject'     => 'A sign saying "Please Don\'t Drink the Emperor"',
                'responsible' => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela, Zoidberg',
                'delivery_to' => 'Emperor Bont',
                'delivery_at' => 'Trisol',
                'notes'       => null,
                'date'        => '1999-05-04',
                'notes2'      => null,
            ],
            '1ACV04' => [
                'subject'     => 'Subpoenas',
                'responsible' => 'user:leela',
                'crew'        => 'Fry, Leela, Bender',
                'delivery_to' => 'Possibly Big Vinnie',
                'delivery_at' => 'Sicily 8',
                'notes'       => null,
                'date'        => '1999-05-18',
                'notes2'      => null,
            ],
            '1ACV06' => [
                'subject'     => 'Guenter',
                'responsible' => 'user:hubert',
                'crew'        => 'Bender, Fry, Leela, Prof. Farnsworth',
                'delivery_to' => 'Prof. Farnsworth',
                'delivery_at' => 'Mars University',
                'notes'       => 'Delivery to the Professor\'s office at Mars University',
                'date'        => '1999-10-03',
                'notes2'      => null,
            ],
            '2ACV01' => [
                'subject'     => 'Ceremonial oversized Scissors',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'DOOP',
                'delivery_at' => 'DOOP headquarters',
                'notes'       => null,
                'date'        => '1999-11-28',
                'notes2'      => 'Delivery intercepted by Zapp Brannigan',
            ],
            '2ACV02' => [
                'subject'     => 'Pillows',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Hotel management',
                'delivery_at' => 'Hotel, Stumbos 4',
                'notes'       => null,
                'date'        => '1999-12-29',
                'notes2'      => null,
            ],
            '2ACV03' => [
                'subject'     => 'Atom of jumbonium for the Miss Universe Pageant',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Bob Barker\'s head',
                'delivery_at' => 'Tova 9',
                'notes'       => null,
                'date'        => '2000-02-20',
                'notes2'      => 'Delivery disrupted by Bender\'s theft of the atom.',
            ],
            '2ACV04' => [
                'subject'     => 'Popcorn',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'People of Cineplex 14',
                'delivery_at' => 'Cineplex 14',
                'notes'       => null,
                'date'        => '2000-03-06',
                'notes2'      => 'Delivery aborted when Leela received an email from Alcazar',
            ],
            '3ACV01' => [
                'subject'     => 'Letters for Santa',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robot Santa Claus',
                'delivery_at' => 'Neptune',
                'notes'       => null,
                'date'        => '2001-12-23',
                'notes2'      => null,
            ],
            '3ACV02' => [
                'subject'     => 'A sandstone block',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Osirians',
                'delivery_at' => 'Osiris 4',
                'notes'       => null,
                'date'        => '2002-03-10',
                'notes2'      => null,
            ],
            '4ACV01' => [
                'subject'     => 'Medication',
                'responsible' => 'user:amy',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Hive mind of Nigel 7',
                'delivery_at' => 'Nigel 7',
                'notes'       => null,
                'date'        => '2003-01-12',
                'notes2'      => 'Delivery failed due to Amy Wong commandeering the Planet Express ship.',
            ],
            '4ACV02' => [
                'subject'     => 'Candy hearts',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Lrrr and Ndnd',
                'delivery_at' => 'Omicron Persei 8',
                'notes'       => null,
                'date'        => '2002-02-10',
                'notes2'      => 'Delivery aborted after Omicronians began attacking the crew. Hearts dumped into quasar.',
            ],
            '4ACV03' => [
                'subject'     => 'Ice from Halley\'s Comet',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'The people of Earth',
                'delivery_at' => 'Earth',
                'notes'       => null,
                'date'        => '2002-11-10',
                'notes2'      => 'Delivery failed due to the comet running out of ice.',
            ],
            '5ACV01' => [
                'subject'     => 'Barstool softener',
                'responsible' => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Nude Bartender',
                'delivery_at' => 'Planet XXX',
                'notes'       => null,
                'date'        => '2007-11-27',
                'notes2'      => null,
            ],
            '5ACV02' => [
                'subject'     => 'Billion-mile security fence',
                'responsible' => 'user:hubert',
                'crew'        => 'Hermes, Prof. Farnsworth, Scruffy, Zoidberg',
                'delivery_to' => 'Leo Wong',
                'delivery_at' => 'Deep Space',
                'notes'       => null,
                'date'        => '2009-02-23',
                'notes2'      => 'Delivery intercepted by Feministas',
            ],
            '6ACV01' => [
                'subject'     => 'e-Waste',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Antarian workers',
                'delivery_at' => 'Third World of the Antares system',
                'notes'       => null,
                'date'        => '2010-07-01',
                'notes2'      => null,
            ],
            '6ACV02' => [
                'subject'     => 'A soufflé laced with nitroglycerine',
                'responsible' => 'user:hubert',
                'crew'        => 'Amy, Bender, Hermes, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Mrs. Astor',
                'delivery_at' => 'Waldorf Asteroid',
                'notes'       => null,
                'date'        => '2010-09-02',
                'notes2'      => null,
            ],
            '6ACV03' => [
                'subject'     => 'New clamps for Francis X. Clampazzo.',
                'responsible' => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Francis X. Clampazzo',
                'delivery_at' => 'The Donbot\'s mansion, Long Long Island',
                'notes'       => null,
                'date'        => '2010-11-21',
                'notes2'      => null,
            ],
            '6ACV04' => [
                'subject'     => '200 feet of hanging rope for the hanging of multiheaded monster.',
                'responsible' => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Hermes, Leela, the Professor, Zoidberg',
                'delivery_to' => 'Sheriff Burley',
                'delivery_at' => 'Aldrin\'s Gulch Town Jail, Aldrin\'s Gulch, Moon',
                'notes'       => null,
                'date'        => '2011-07-14',
                'notes2'      => null,
            ],
            '6ACV08' => [
                'subject'     => 'A statue commemorating the loss of the first Planet Express crew',
                'responsible' => 'user:leela',
                'crew'        => 'Fry, Bender, Leela, Hermes, Amy, and Zoidberg',
                'delivery_to' => 'Professor Farnsworth',
                'delivery_at' => 'Planet Express headquarters',
                'notes'       => null,
                'date'        => '2011-08-04',
                'notes2'      => null,
            ],
        ];

        $state_new       = $this->getReference('state:new');
        $state_delivered = $this->getReference('state:delivered');

        foreach ($records as $info) {

            $class = new \ReflectionClass(Record::class);

            /** @var Record $record */
            $record = $class->newInstanceWithoutConstructor();

            $record->setSubject($info['subject']);

            $altr_record = $this->ego($record);

            $altr_record->state       = $state_new;
            $altr_record->author      = $this->getReference('user:hubert');
            $altr_record->responsible = $this->getReference($info['responsible']);
            $altr_record->createdAt   = strtotime($info['date'] . ' 09:00:00');
            $altr_record->changedAt   = strtotime($info['date'] . ' 09:00:00');

            $event = new Event(
                $record,
                $record->getAuthor(),
                EventType::RECORD_CREATED,
                $state_new->getId()
            );

            $event2 = new Event(
                $record,
                $record->getAuthor(),
                EventType::RECORD_ASSIGNED,
                $record->getResponsible()->getId()
            );

            $this->ego($event)->createdAt  = $record->getCreatedAt();
            $this->ego($event2)->createdAt = $record->getCreatedAt();

            $manager->persist($record);
            $manager->persist($event);
            $manager->persist($event2);

            $manager->flush();

            $values = [
                1 => $info['crew'],
                2 => $info['delivery_to'],
                3 => $info['delivery_at'],
            ];

            for ($i = 1; $i <= 3; $i++) {

                $value = $this->container->get('doctrine')->getRepository(StringValue::class)->findOneBy([
                    'token' => md5($values[$i]),
                ]);

                if (!$value) {

                    $value = new StringValue($values[$i]);

                    $manager->persist($value);
                    $manager->flush();
                }

                $field = new FieldValue();

                $this->ego($field)->event     = $event;
                $this->ego($field)->field     = $this->getReference('state:new:' . $i);
                $this->ego($field)->isCurrent = true;
                $this->ego($field)->value     = $value->getId();

                $manager->persist($field);
            }

            $field = new FieldValue();

            $this->ego($field)->event     = $event;
            $this->ego($field)->field     = $this->getReference('state:new:4');
            $this->ego($field)->isCurrent = true;

            if ($info['notes']) {

                $value = new TextValue($info['notes']);

                $manager->persist($value);
                $manager->flush();

                $this->ego($field)->value = $value->getId();
            }

            $read = new LastRead();

            $this->ego($read)->record = $record;
            $this->ego($read)->user   = $record->getAuthor();
            $this->ego($read)->readAt = $record->getCreatedAt();

            $manager->persist($field);
            $manager->persist($read);

            if ($info['date'] < '2010-01-01') {

                $event = new Event(
                    $record,
                    $record->getResponsible(),
                    EventType::STATE_CHANGED,
                    $state_delivered->getId()
                );

                $this->ego($event)->createdAt = strtotime($info['date'] . ' 17:00:00');

                $manager->persist($event);
                $manager->flush();

                $field = new FieldValue();

                $this->ego($field)->event     = $event;
                $this->ego($field)->field     = $this->getReference('state:delivered:1');
                $this->ego($field)->isCurrent = true;

                if ($info['notes2']) {

                    $value = new TextValue($info['notes2']);

                    $manager->persist($value);
                    $manager->flush();

                    $this->ego($field)->value = $value->getId();
                }

                $altr_record->changedAt   = $this->ego($event)->createdAt;
                $altr_record->closedAt    = $this->ego($event)->createdAt;
                $altr_record->state       = $state_delivered;
                $altr_record->responsible = null;

                $read = $this->container->get('doctrine')->getRepository(LastRead::class)->findOneBy([
                    'record' => $record,
                    'user'   => $this->ego($event)->user,
                ]);

                if (!$read) {

                    $read = new LastRead();

                    $this->ego($read)->record = $record;
                    $this->ego($read)->user   = $this->ego($event)->user;
                }

                $this->ego($read)->readAt = $this->ego($event)->createdAt;

                $manager->persist($field);
                $manager->persist($record);
                $manager->persist($read);
            }
        }

        $manager->flush();
    }
}
