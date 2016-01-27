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

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Entity\DecimalValue;
use eTraxis\Entity\Event;
use eTraxis\Entity\Field;
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Entity\StringValue;
use eTraxis\Entity\TextValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRecordsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
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
        $this->loadDeliveryRecords($manager);
        $this->loadFuturamaRecords($manager);
    }

    /**
     * Loads records of "Delivery" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadDeliveryRecords(ObjectManager $manager)
    {
        $records = [
            '1ACV01' => [
                'subject'     => 'Prizes for the claw crane',
                'assignee'    => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Sal',
                'delivery_at' => 'Luna Park, Moon',
                'notes'       => null,
                'date'        => '1999-04-04',
                'notes2'      => null,
            ],
            '1ACV02' => [
                'subject'     => 'Lug nuts',
                'assignee'    => 'user:bender',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robots of Chapek 9',
                'delivery_at' => 'Chapek 9',
                'notes'       => 'Only Bender goes on the planet, because humans would be killed.',
                'date'        => '1999-04-20',
                'notes2'      => null,
            ],
            '1ACV03' => [
                'subject'     => 'A sign saying "Please Don\'t Drink the Emperor"',
                'assignee'    => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela, Zoidberg',
                'delivery_to' => 'Emperor Bont',
                'delivery_at' => 'Trisol',
                'notes'       => null,
                'date'        => '1999-05-04',
                'notes2'      => null,
            ],
            '1ACV04' => [
                'subject'     => 'Subpoenas',
                'assignee'    => 'user:leela',
                'crew'        => 'Fry, Leela, Bender',
                'delivery_to' => 'Possibly Big Vinnie',
                'delivery_at' => 'Sicily 8',
                'notes'       => null,
                'date'        => '1999-05-18',
                'notes2'      => null,
            ],
            '1ACV06' => [
                'subject'     => 'Guenter',
                'assignee'    => 'user:hubert',
                'crew'        => 'Bender, Fry, Leela, Prof. Farnsworth',
                'delivery_to' => 'Prof. Farnsworth',
                'delivery_at' => 'Mars University',
                'notes'       => 'Delivery to the Professor\'s office at Mars University',
                'date'        => '1999-10-03',
                'notes2'      => null,
            ],
            '2ACV01' => [
                'subject'     => 'Ceremonial oversized Scissors',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'DOOP',
                'delivery_at' => 'DOOP headquarters',
                'notes'       => null,
                'date'        => '1999-11-28',
                'notes2'      => 'Delivery intercepted by Zapp Brannigan',
            ],
            '2ACV02' => [
                'subject'     => 'Pillows',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Hotel management',
                'delivery_at' => 'Hotel, Stumbos 4',
                'notes'       => null,
                'date'        => '1999-12-29',
                'notes2'      => null,
            ],
            '2ACV03' => [
                'subject'     => 'Atom of jumbonium for the Miss Universe Pageant',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Bob Barker\'s head',
                'delivery_at' => 'Tova 9',
                'notes'       => null,
                'date'        => '2000-02-20',
                'notes2'      => 'Delivery disrupted by Bender\'s theft of the atom.',
            ],
            '2ACV04' => [
                'subject'     => 'Popcorn',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'People of Cineplex 14',
                'delivery_at' => 'Cineplex 14',
                'notes'       => null,
                'date'        => '2000-03-06',
                'notes2'      => 'Delivery aborted when Leela received an email from Alcazar',
            ],
            '3ACV01' => [
                'subject'     => 'Letters for Santa',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robot Santa Claus',
                'delivery_at' => 'Neptune',
                'notes'       => null,
                'date'        => '2001-12-23',
                'notes2'      => null,
            ],
            '3ACV02' => [
                'subject'     => 'A sandstone block',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Osirians',
                'delivery_at' => 'Osiris 4',
                'notes'       => null,
                'date'        => '2002-03-10',
                'notes2'      => null,
            ],
            '4ACV01' => [
                'subject'     => 'Medication',
                'assignee'    => 'user:amy',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Hive mind of Nigel 7',
                'delivery_at' => 'Nigel 7',
                'notes'       => null,
                'date'        => '2003-01-12',
                'notes2'      => 'Delivery failed due to Amy Wong commandeering the Planet Express ship.',
            ],
            '4ACV02' => [
                'subject'     => 'Candy hearts',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Lrrr and Ndnd',
                'delivery_at' => 'Omicron Persei 8',
                'notes'       => null,
                'date'        => '2002-02-10',
                'notes2'      => 'Delivery aborted after Omicronians began attacking the crew. Hearts dumped into quasar.',
            ],
            '4ACV03' => [
                'subject'     => 'Ice from Halley\'s Comet',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'The people of Earth',
                'delivery_at' => 'Earth',
                'notes'       => null,
                'date'        => '2002-11-10',
                'notes2'      => 'Delivery failed due to the comet running out of ice.',
            ],
            '5ACV01' => [
                'subject'     => 'Barstool softener',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Nude Bartender',
                'delivery_at' => 'Planet XXX',
                'notes'       => null,
                'date'        => '2007-11-27',
                'notes2'      => null,
            ],
            '5ACV02' => [
                'subject'     => 'Billion-mile security fence',
                'assignee'    => 'user:hubert',
                'crew'        => 'Hermes, Prof. Farnsworth, Scruffy, Zoidberg',
                'delivery_to' => 'Leo Wong',
                'delivery_at' => 'Deep Space',
                'notes'       => null,
                'date'        => '2009-02-23',
                'notes2'      => 'Delivery intercepted by Feministas',
            ],
            '6ACV01' => [
                'subject'     => 'e-Waste',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Antarian workers',
                'delivery_at' => 'Third World of the Antares system',
                'notes'       => null,
                'date'        => '2010-07-01',
                'notes2'      => null,
            ],
            '6ACV02' => [
                'subject'     => 'A soufflé laced with nitroglycerine',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Hermes, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Mrs. Astor',
                'delivery_at' => 'Waldorf Asteroid',
                'notes'       => null,
                'date'        => '2010-09-02',
                'notes2'      => null,
            ],
            '6ACV03' => [
                'subject'     => 'New clamps for Francis X. Clampazzo.',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Francis X. Clampazzo',
                'delivery_at' => 'The Donbot\'s mansion, Long Long Island',
                'notes'       => null,
                'date'        => '2010-11-21',
                'notes2'      => null,
            ],
            '6ACV04' => [
                'subject'     => '200 feet of hanging rope for the hanging of multiheaded monster.',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Hermes, Leela, the Professor, Zoidberg',
                'delivery_to' => 'Sheriff Burley',
                'delivery_at' => 'Aldrin\'s Gulch Town Jail, Aldrin\'s Gulch, Moon',
                'notes'       => null,
                'date'        => '2011-07-14',
                'notes2'      => null,
            ],
            '6ACV08' => [
                'subject'     => 'A statue commemorating the loss of the first Planet Express crew',
                'assignee'    => 'user:leela',
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

            $record = new Record();

            /** @noinspection PhpParamsInspection */
            $record
                ->setSubject($info['subject'])
                ->setCreatedAt(strtotime($info['date'] . ' 09:00:00'))
                ->setChangedAt(strtotime($info['date'] . ' 09:00:00'))
                ->setResumedAt(0)
                ->setState($state_new)
                ->setAuthor($this->getReference('user:hubert'))
                ->setResponsible($this->getReference($info['assignee']))
            ;

            $event = new Event();

            $event
                ->setType(Event::RECORD_CREATED)
                ->setCreatedAt($record->getCreatedAt())
                ->setParameter($state_new->getId())
                ->setRecord($record)
                ->setUser($record->getAuthor())
            ;

            $event2 = new Event();

            $event2
                ->setType(Event::RECORD_ASSIGNED)
                ->setCreatedAt($record->getCreatedAt())
                ->setParameter($record->getResponsible()->getId())
                ->setRecord($record)
                ->setUser($record->getAuthor())
            ;

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

                $value = $this->container->get('doctrine')->getRepository('eTraxis:StringValue')->findOneBy([
                    'token' => md5($values[$i]),
                ]);

                if (!$value) {

                    $value = new StringValue();
                    $value->setToken(md5($values[$i]));
                    $value->setValue($values[$i]);

                    $manager->persist($value);
                    $manager->flush();
                }

                $field = new FieldValue();

                /** @noinspection PhpParamsInspection */
                $field
                    ->setEventId($event->getId())
                    ->setFieldId($this->getReference('state:new:' . $i)->getId())
                    ->setType(Field::TYPE_STRING)
                    ->setValueId($value->getId())
                    ->setCurrent(true)
                    ->setEvent($event)
                    ->setField($this->getReference('state:new:' . $i))
                ;

                $manager->persist($field);
            }

            $field = new FieldValue();

            /** @noinspection PhpParamsInspection */
            $field
                ->setEventId($event->getId())
                ->setFieldId($this->getReference('state:new:4')->getId())
                ->setType(Field::TYPE_TEXT)
                ->setCurrent(true)
                ->setEvent($event)
                ->setField($this->getReference('state:new:4'))
            ;

            if ($info['notes']) {

                $value = new TextValue();
                $value->setToken(md5($info['notes']));
                $value->setValue($info['notes']);

                $manager->persist($value);
                $manager->flush();

                $field->setValueId($value->getId());
            }

            $read = new LastRead();

            $read
                ->setRecordId($record->getId())
                ->setUserId($record->getAuthor()->getId())
                ->setReadAt($record->getCreatedAt())
                ->setRecord($record)
                ->setUser($record->getAuthor())
            ;

            $manager->persist($field);
            $manager->persist($read);

            if ($info['date'] < '2010-01-01') {

                $event = new Event();

                $event
                    ->setType(Event::STATE_CHANGED)
                    ->setCreatedAt(strtotime($info['date'] . ' 17:00:00'))
                    ->setParameter($state_delivered->getId())
                    ->setRecord($record)
                    ->setUser($record->getResponsible())
                ;

                $manager->persist($event);
                $manager->flush();

                $field = new FieldValue();

                /** @noinspection PhpParamsInspection */
                $field
                    ->setEventId($event->getId())
                    ->setFieldId($this->getReference('state:delivered:1')->getId())
                    ->setType(Field::TYPE_TEXT)
                    ->setCurrent(true)
                    ->setEvent($event)
                    ->setField($this->getReference('state:delivered:1'))
                ;

                if ($info['notes2']) {

                    $value = new TextValue();
                    $value->setToken(md5($info['notes2']));
                    $value->setValue($info['notes2']);

                    $manager->persist($value);
                    $manager->flush();

                    $field->setValueId($value->getId());
                }

                /** @noinspection PhpParamsInspection */
                $record
                    ->setClosedAt($event->getCreatedAt())
                    ->setState($state_delivered)
                    ->setResponsible(null)
                ;

                $read = $this->container->get('doctrine')->getRepository('eTraxis:LastRead')->findOneBy([
                    'recordId' => $record->getId(),
                    'userId'   => $event->getUser()->getId(),
                ]);

                if (!$read) {

                    $read = new LastRead();

                    $read
                        ->setRecordId($record->getId())
                        ->setUserId($event->getUser()->getId())
                        ->setRecord($record)
                        ->setUser($event->getUser())
                    ;
                }

                $read->setReadAt($event->getCreatedAt());

                $manager->persist($field);
                $manager->persist($record);
                $manager->persist($read);
            }
        }

        $manager->flush();
    }

    /**
     * Loads records of "Futurama" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaRecords(ObjectManager $manager)
    {
        $records = [
            '1ACV01' => [
                'subject'   => 'Space Pilot 3000',
                'season'    => 1,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Pizza delivery boy Philip J. Fry accidentally stumbles into a cryogenic freezer on December 31, 1999, and awakens one thousand years in the future on New Year\'s Eve, 2999. He meets a one-eyed career counselor named Leela, who tries to assign him an eternal career as a delivery boy. Fry dislikes the idea and escapes into the city where he meets Bender, an alcoholic robot who has also abandoned his job, and the two become friends. Fry soon becomes depressed that he can never return to his old life and surrenders to Leela, but she realizes that she also hates her job and quits. Now fugitives, the three visit Fry\'s descendant, Professor Farnsworth, who helps them escape from the police on his intergalactic spaceship as the world celebrates the year 3000. Farnsworth hires the three to become his crew for his intergalactic delivery service, Planet Express, with Fry becoming a delivery boy.',
                'delivery'  => '1ACV01',
                'date'      => '1999-03-28',
                'viewers'   => null,
            ],
            '1ACV02' => [
                'subject'   => 'The Series Has Landed',
                'season'    => 1,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The new Planet Express crew receive their first mission: a delivery to an amusement park on the Moon. Fry is enthusiastic about the idea of going to the Moon, but is disappointed that people only go there for the amusement park and wants to see the "real Moon". He hijacks one of the rides with Leela, but gets them both stranded on the Moon\'s surface. Low on oxygen, they take refuge in a hydroponic farm, but Bender, who was kicked out of the amusement park, makes advances on one of the farmer\'s robot daughters, forcing the three to flee from the angry farmer. Fry and Leela find and take shelter in the Apollo 11 lander until all three are rescued by Planet Express intern Amy Wong.',
                'delivery'  => '1ACV02',
                'date'      => '1999-04-04',
                'viewers'   => null,
            ],
            '1ACV03' => [
                'subject'   => 'I, Roommate',
                'season'    => 1,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry lives in the Planet Express building until he is kicked out for his messiness. He initially moves in with Bender, but his new apartment is little more than a cramped closet, so they both move into a more spacious and furnished apartment. During the housewarming party, it is discovered that Bender\'s antenna interferes with the entire building\'s satellite TV reception, and Bender is evicted while Fry stays with little regard for his friend\'s troubles. Depressed, Bender goes on a self-destructive sobriety binge until he cuts off his antenna in the hopes of moving back with Fry. Realizing that Bender\'s antenna is vital to his self-esteem, Fry helps Bender reattach it and moves back into Bender\'s old apartment. It is then revealed that Bender\'s apartment has a "closet" that is the size of a complete living suite with more than enough room for Fry, so Fry decides to move there instead.',
                'delivery'  => '1ACV03',
                'date'      => '1999-04-06',
                'viewers'   => null,
            ],
            '1ACV04' => [
                'subject'   => 'Love\'s Labours Lost in Space',
                'season'    => 1,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew go on a mission to rescue animals on Vergon 6, a planet that was mined hollow of the dark matter that filled it for fuel, and is on the verge of collapse. On the way, they meet Zapp Brannigan, who Leela is initially flattered to meet until he refuses to help them save the animals of Vergon 6 as per the law and imprisons them instead. That night, Zapp tries to seduce Leela, succeeding in getting her to sympathize with his loneliness as a captain, and she has sex with him out of pity. The next day, Zapp decides to release the crew and allows them to travel to Vergon 6, believing Leela will come crawling back to him. While collecting the animals, Leela discovers another creature not on their list and decides to save it as well, naming it Nibbler, who subsequently devours all the other animals they saved. The planet begins to collapse and the crew find their ship is out of fuel. Leela refuses to accept Zapp\'s help when he tells them Nibbler must remain behind, and the crew resign to their fate until Nibbler excretes a pellet of dark matter, which gives them enough fuel to escape before the planet implodes.',
                'delivery'  => '1ACV04',
                'date'      => '1999-04-13',
                'viewers'   => null,
            ],
            '1ACV05' => [
                'subject'   => 'Fear of a Bot Planet',
                'season'    => 1,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew deliver a package to Chapek 9, a planet inhabited entirely by human-hating robots who kill humans on sight. Bender is sent to deliver the package alone, but is captured upon discovery that he works for humans, so Fry and Leela disguise themselves as robots and infiltrate the robot society. When they find Bender, they discover that Bender has made himself an idol among the other robots out of frustration of feeling unappreciated by his crew. Fry and Leela are captured, but Bender refuses to kill his friends. They soon learn that the planet\'s government is merely using humans as a scapegoat to distract the population from a valuable lug nut shortage. The three escape the planet and Bender, remembering he forgot to deliver the package, drops it onto the robots chasing them, revealing a shower of lug nuts and causing the robots to renounce their human-hating ways.',
                'delivery'  => null,
                'date'      => '1999-04-20',
                'viewers'   => null,
            ],
            '1ACV06' => [
                'subject'   => 'A Fishful of Dollars',
                'season'    => 1,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry checks his bank account for the first time in a thousand years and discovers that he has become a multi-billionaire thanks to compound interest. He goes on a massive spending spree and buys various 20th century artifacts, including the last unopened can of anchovies, which have gone extinct. Mom, a famous industrialist and oil tycoon, feels threatened that the anchovies\' oil could be used to put her out of business, so she sends her sons to steal Fry\'s ATM and PIN. Fry\'s bank account is emptied and his 20th century artifacts are repossessed except for the anchovies, which Mom hopes Fry will sell to her. However, she stops interfering once she learns that Fry intends to serve the anchovies on a pizza to share with his friends, who end up hating it.',
                'delivery'  => '1ACV06',
                'date'      => '1999-04-27',
                'viewers'   => null,
            ],
            '1ACV07' => [
                'subject'   => 'My Three Suns',
                'season'    => 1,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The crew are sent to make a delivery on the planet of Trisol, but Fry is stricken by extreme thirst after traveling on foot under the planet\'s three blazing suns and, upon arrival at the planetary palace, drinks what appears to be a bottle of water. It turns out that the "water" is in fact the emperor of the planet\'s liquid-based inhabitants, and Fry is declared the planet\'s new emperor. Before Fry\'s coronation, Leela tries to warn him that each of the planet\'s emperors have been killed and succeeded on a weekly basis, but Fry refuses to listen and Leela vows to never help him again. When the sun sets after Fry\'s coronation, the Trisolians begin to glow, including the previous emperor, who is still alive in Fry\'s stomach and orders him to be cut open and drained. Leela ultimately decides to help save Fry from being killed by beating him up, causing Fry to weep in pain and gradually cry out the emperor.',
                'delivery'  => null,
                'date'      => '1999-05-04',
                'viewers'   => null,
            ],
            '1ACV08' => [
                'subject'   => 'A Big Piece of Garbage',
                'season'    => 1,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After forgetting to bring an invention to an academic symposium and being humiliated by his arch-nemesis, Ogden Wernstrom, Professor Farnsworth discovers another invention he attempted to substitute, the "Smell-O-Scope", and uses it to discover than an enormous ball of garbage that was launched into space in the year 2000 is now on a collision course back to Earth. The Planet Express crew are sent to destroy it in space with explosives, but Professor Farnsworth blunders the installation of the bomb\'s timer and the plan fails. In desperation, Farnsworth proposes they deflect the ball into the Sun by launching another ball of garbage into it. Fry teaches the city how to make garbage, since everyone forgot how to do so over the centuries. The plan succeeds while the new ball flies out into the solar system, and Farnsworth regains his honor.',
                'delivery'  => null,
                'date'      => '1999-05-11',
                'viewers'   => null,
            ],
            '1ACV09' => [
                'subject'   => 'Hell Is Other Robots',
                'season'    => 1,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender becomes addicted to electricity and ends up wrecking the Planet Express ship while steering it into an electrical storm in space, nearly killing everyone. After being confronted about his addiction, Bender realizes he has a problem and finds religion at the Temple of Robotology. He begins repenting for his evil ways, but annoys and disturbs his fellow crew members in doing so. Deciding they want the old Bender back, Fry and Leela persuade him to revert to his former self. As punishment for turning his back on his faith, Bender is sent to Robot Hell, but Fry and Leela find and save him from eternal damnation at the hands of the Robot Devil.',
                'delivery'  => null,
                'date'      => '1999-05-18',
                'viewers'   => null,
            ],
            '1ACV10' => [
                'subject'   => 'A Flight to Remember',
                'season'    => 1,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew take a luxury cruise on the largest space ship ever built: the Titanic. On board, Bender meets the lovely robot Countess de la Roca. At first, he is only attracted to her for her wealth and pretends to be rich himself, but the two genuinely fall in love even after Bender\'s secret is exposed. Meanwhile, to avoid the advances of the Titanic‍ \'​s captain, Zapp, Leela pretends to be engaged to Fry. However, Amy passes herself off as Fry\'s girlfriend to her parents to keep them from meddling with her love life, which makes Leela jealous. Before the fake relationships are exposed, the Titanic becomes entangled in a swarm of comets as a result of Zapp changing course and is piloted into a black hole. The Planet Express crew are safely evacuated on the ship\'s escape pods, with the Countess sacrificing herself to save Bender\'s life.',
                'delivery'  => null,
                'date'      => '1999-09-26',
                'viewers'   => null,
            ],
            '1ACV11' => [
                'subject'   => 'Mars University',
                'season'    => 1,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry attends Mars University with the intention of dropping out of college and becomes the roommate of Guenter, a monkey who is made intelligent by an electronium hat provided by the Professor. Fry becomes bitter rivals with Guenter and humiliates him during the parents\' reception party by releasing his unintelligent, feral parents, which makes Guenter unhappy about his current lifestyle. Guenter gradually becomes stressed to the point of taking off his hat and fleeing into the Martian jungle. Fry, Leela, and Farnsworth find him and try to make him choose between an intelligent life and the life of a normal monkey, but the three are swept into a river by Bender during a raft regatta. Guenter falls off a cliff after saving the three from falling over a waterfall using the intelligence provided by his hat. The hat breaks his fall and begins working at half its usual capacity, and Guenter becomes content with his now average intelligence.',
                'delivery'  => null,
                'date'      => '1999-10-03',
                'viewers'   => null,
            ],
            '1ACV12' => [
                'subject'   => 'When Aliens Attack',
                'season'    => 1,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Earth is invaded by aliens from Omicron Persei 8, who seemingly demand the planet hand over its president. It turns out, however, that the one they are referring to is the main character of a 20th century TV show, Single Female Lawyer, the final episode of which was disrupted before it was concluded when Fry accidentally spilled beer on a control panel from the station it was broadcast from back in 1999. The Omicronians threaten Earth to broadcast the episode or be destroyed, but because no copy of the episode exists anymore, the Planet Express crew are forced to reenact it. The resulting product is crude, but with Fry\'s guidance, the Omicronians are satisfied with the ending and leave the partially destroyed Earth.',
                'delivery'  => null,
                'date'      => '1999-11-07',
                'viewers'   => null,
            ],
            '1ACV13' => [
                'subject'   => 'Fry and the Slurm Factory',
                'season'    => 1,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry wins a contest that allows him and the Planet Express crew to tour the factory where his favorite soft drink, Slurm, is made. During the tour, Fry, Leela, and Bender stumble into the factory\'s underbelly where they discover that the drink is actually the secretion of a giant worm, the Slurm Queen, as part of a money-making plot. The Queen discovers the three and, fearing her company will be ruined if the scandal is exposed, attempts to silence them, but they escape. However, Fry cannot bring himself to ruin his favorite drink, so the Planet Express crew decide to keep the plot a secret.',
                'delivery'  => null,
                'date'      => '1999-11-14',
                'viewers'   => null,
            ],
            '2ACV01' => [
                'subject'   => 'I Second That Emotion',
                'season'    => 2,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'During Nibbler\'s birthday party, Bender becomes annoyed at how little attention he is getting and flushes Nibbler down the toilet. Distraught, Leela wishes that Bender could understand human emotions, so Professor Farnsworth installs an empathy chip in Bender so that he can feel Leela\'s emotions as she feels them. Leela\'s sadness of losing Nibbler becomes too great for Bender, who flushes himself down the toilet to find and rescue Nibbler in the sewers. Fry and Leela follow Bender and encounter a group of sewer mutants who live in fear of a monster called El Chupanibre, believing it to be Nibbler. Nibbler returns safely, but it turns out that he is not the monster, which appears alongside him. Bender tries to fight El Chupanibre, but Leela\'s fear for Nibbler\'s safety immobilizes him, so he convinces her to think only about herself rather than others, which gives Bender the strength to drive off the monster. Bender\'s empathy chip is later removed, though he has learned nothing.',
                'delivery'  => '2ACV01',
                'date'      => '1999-11-21',
                'viewers'   => null,
            ],
            '2ACV02' => [
                'subject'   => 'Brannigan, Begin Again',
                'season'    => 2,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Zapp Brannigan and his subordinate Kif are court-martialed and dishonorably discharged after destroying the new DOOP headquarters. Unemployed, Zapp and Kif take up jobs at Planet Express. During a mission, Zapp plays upon Leela\'s harsh treatment of Fry and Bender and convinces the two to stage a mutiny so that he may attack a neutral planet, hoping it will get him reinstated in the DOOP. Fry and Bender discover the plan is a suicide mission, so they free Leela and foil Zapp\'s plan. Zapp later tries to take credit for the Planet Express crew\'s heroics, and he and Kif are reinstated in the DOOP after Leela supports his testimony to keep Zapp away from her.',
                'delivery'  => '2ACV02',
                'date'      => '1999-11-28',
                'viewers'   => null,
            ],
            '2ACV03' => [
                'subject'   => 'A Head in the Polls',
                'season'    => 2,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After the price of titanium skyrockets, Bender pawns his titanium-rich body. Now nothing more than a head, Bender begins to live glamorously until he meets the head of former president Richard Nixon, who tells him that life as a head is actually depressing. Bender decides to buy his body back, only to find it has already been bought by Nixon so that he may participate in the current election race for President of Earth. Fry, Leela, and Bender confront Nixon over Bender\'s body, and Nixon rants about his devious future plans for Earth, which Bender records and uses to blackmail Nixon into giving his body back. The three believe they have defeated Nixon, but because Leela forgot to vote, Nixon wins by a single vote thanks to acquiring a new, giant war robot\'s body.',
                'delivery'  => '2ACV03',
                'date'      => '1999-12-12',
                'viewers'   => null,
            ],
            '2ACV04' => [
                'subject'   => 'Xmas Story',
                'season'    => 2,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry becomes homesick during his first 31st century Christmas, called "Xmas". His insensitive moping hurts Leela\'s feelings because she has no family to celebrate Xmas with, and she runs off in tears. Fry goes out to buy Leela a present to apologize, but the others warn him to return by sundown, or else he will encounter Robot Santa, a murderous robot who kills anyone he believes to be "naughty", which is practically everyone. Fry finds Leela and the two reconcile, but it gets late and they are attacked by Robot Santa on their way back home. Robot Santa tries to break into the building, but everyone teams up to drive him away. Everyone celebrates their victory over Robot Santa, who threatens to return next Xmas.',
                'delivery'  => '2ACV04',
                'date'      => '1999-12-19',
                'viewers'   => null,
            ],
            '2ACV05' => [
                'subject'   => 'Why Must I Be a Crustacean in Love?',
                'season'    => 2,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Zoidberg\'s recent aggressive behavior indicates it is mating season for his species. Zoidberg goes with the crew to his home planet of Decapod 10 for the mating frenzy, but his erotic display fails to attract any mates, including a female Decapodian and an old acquaintance of Zoidberg\'s named Edna. Fry teaches Zoidberg how to win Edna\'s heart through romance, but she begins to grow attracted to Fry instead upon learning that he is responsible for Zoidberg\'s romantic behavior. Enraged, Zoidberg challenges Fry to a fight to the death over Edna\'s affections, but the other Decapodians leave partway through the fight to participate in the mating frenzy, including Edna, who mates with the Decapodian king instead. Having missed out on the mating frenzy, Zoidberg reveals that everyone who participates in it dies after laying their eggs and apologizes to Fry.',
                'delivery'  => null,
                'date'      => '2000-02-06',
                'viewers'   => null,
            ],
            '2ACV06' => [
                'subject'   => 'The Lesser of Two Evils',
                'season'    => 2,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew meets another bending robot named Flexo, who bears a striking resemblance to Bender, save for a goatee. Bender and Flexo become friends, but Fry suspects Flexo to be Bender\'s evil twin. Flexo joins the crew on their way to the Miss Universe pageant to help guard a valuable Jumbonium atom, but when they arrive at the pageant, the atom is missing and Fry immediately suspects Flexo. Bender gets into a fight with Flexo to stop him until it is revealed that Bender is the one who stole the atom. The atom is recovered, but Flexo is accidentally imprisoned for Bender\'s crime.',
                'delivery'  => null,
                'date'      => '2000-02-20',
                'viewers'   => null,
            ],
            '2ACV07' => [
                'subject'   => 'Put Your Head on My Shoulders',
                'season'    => 2,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry goes with Amy to try out her newly purchased car on Mercury. When the car runs out of gas, the two make love while waiting for a tow truck and begin dating. Valentine\'s Day approaches and Fry begins to feel smothered by Amy, but as Fry prepares to break up with her, they get into a car accident that nearly kills him. With his body severely damaged, Fry\'s head is kept alive by being grafted onto Amy\'s shoulder. Fry breaks up with Amy anyway upon returning to Earth, so she arranges another date that Fry is forced to attend. Before Fry can unwillingly join Amy in an intimate relationship with her boyfriend, Leela saves him by prolonging their conversation and canceling their evening plans. Fry has his head is reattached to his repaired body the next day, and he and Amy continue to remain friends.',
                'delivery'  => null,
                'date'      => '2000-02-13',
                'viewers'   => null,
            ],
            '2ACV08' => [
                'subject'   => 'Raging Bender',
                'season'    => 2,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender inadvertently defeats a champion robot boxer and is hired to become a member of the Ultimate Robot Fighting League. Leela agrees to train Bender to get back at her old sexist martial arts teacher Fnog. Bender becomes more popular with each fight he wins, which all turn out to be fixed, causing him to neglect his training. Soon, however, Bender\'s popularity begins to dwindle and he is instructed to throw the next match against a giant robot called Destructor. Leela only agrees to help Bender upon learning his opponent is trained by Fnog. During the fight, Leela discovers Destructor is being controlled remotely by Fnog, who she fights and beats up. Bender tries to use this opportunity to defeat Destructor, but his opponent falls on him and he loses the match anyway.',
                'delivery'  => null,
                'date'      => '2000-02-27',
                'viewers'   => null,
            ],
            '2ACV09' => [
                'subject'   => 'A Bicyclops Built for Two',
                'season'    => 2,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela meets another cyclops named Alcazar, who appears to be the only other remaining member of their kind following the destruction of their home planet of Cyclopia. Leela moves into Alcazar\'s castle and decides it is her duty to help rebuild their civilization with him, but he begins to treat her like a slave. Fry grows suspicious of Alcazar, but before he can persuade Leela, Alcazar proposes to her and she accepts. During the wedding, Fry and Bender discover that the planet has four other castles, each housing a different alien Alcazar had proposed to and is preparing to marry on the same day. Fry exposes the secret and Alcazar\'s five brides gang up on him, forcing him to reveal his true form as a shapeshifting bug-like alien. As they leave Alcazar\'s planet, Leela begins contemplating the odds of finding the planet she came from.',
                'delivery'  => null,
                'date'      => '2000-03-19',
                'viewers'   => null,
            ],
            '2ACV10' => [
                'subject'   => 'A Clone of My Own',
                'season'    => 2,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Farnsworth celebrates his 150th birthday, but begins to grow concerned about what will become of his inventions after he is gone, so he names a clone of himself, Cubert Farnsworth, to be his successor. However, Cubert shows no interest in becoming an inventor and harshly criticizes all of Farnsworth\'s ideas and inventions. Saddened, Farnsworth leaves a will revealing that he is in fact 160 years old, the age when people are taken to the Near-Death Star, a retirement home-like facility from which they never return. The crew and Cubert rescue Farnsworth while he is still unconscious, but their ship\'s light-speed engines are damaged. Fortunately, a blow Cubert sustained to the head during the escape causes him to gain Farnsworth\'s understanding of how the engines work. The crew safely returns to Earth, and Cubert tells Farnsworth that he has decided to follow in his "father\'s" footsteps.',
                'delivery'  => null,
                'date'      => '2000-04-09',
                'viewers'   => null,
            ],
            '2ACV11' => [
                'subject'   => 'How Hermes Requisitioned His Groove Back',
                'season'    => 2,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Hermes takes a stress-relieving vacation, replacement bureaucrat Morgan Proctor becomes infatuated with Fry. Bender threatens to publicize their affair, but Morgan removes Bender\'s memory and hides it within the cavernous Central Bureaucracy.',
                'delivery'  => null,
                'date'      => '2000-04-02',
                'viewers'   => null,
            ],
            '2ACV12' => [
                'subject'   => 'The Deep South',
                'season'    => 2,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A fishing trip over the ocean takes a turn for the worse when a colossal mouth bass pulls the Planet Express ship to the bottom of the sea. There, Fry falls in love with a mermaid named Umbriel with long blonde hair, and the crew discovers the sunken city of Atlanta, Georgia.',
                'delivery'  => null,
                'date'      => '2000-04-16',
                'viewers'   => null,
            ],
            '2ACV13' => [
                'subject'   => 'Bender Gets Made',
                'season'    => 2,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender takes a new job in the Robot Mafia, but his loyalty is tested when he goes along on the robotic gangster\'s efforts to rob the Planet Express ship.',
                'delivery'  => null,
                'date'      => '2000-04-30',
                'viewers'   => null,
            ],
            '2ACV14' => [
                'subject'   => 'Mother\'s Day',
                'season'    => 2,
                'episode'   => 14,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Mom reprograms the world\'s robots to rebel against humanity. The only hope of salvation is Mom\'s old flame - Professor Farnsworth, who must rekindle his romance with Mom in order to save mankind.',
                'delivery'  => null,
                'date'      => '2000-05-14',
                'viewers'   => null,
            ],
            '2ACV15' => [
                'subject'   => 'The Problem with Popplers',
                'season'    => 2,
                'episode'   => 15,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The crew discovers an irresistible source of food on a distant planet, and brings it back to Earth to be sold at the Fishy Joe\'s restaurant chain. But when it\'s discovered that the so-called "Popplers" are actually Omicronian babies, the Omicronians demand recompense.',
                'delivery'  => null,
                'date'      => '2000-05-07',
                'viewers'   => null,
            ],
            '2ACV16' => [
                'subject'   => 'Anthology of Interest I',
                'season'    => 2,
                'episode'   => 16,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Farnsworth invents a What-If machine that simulates the Planet Express crew\'s wishes: Bender asks what if he were 500 feet (150 m) tall; Leela asks what if she were more impulsive; and Fry asks what if he never came to the future. Terror at 500 Feet – Bender is built on another planet to be 500 feet tall and comes to Earth to wreak havoc. Desperate to put an end to Bender\'s rampage, the Planet Express crew turns Zoidberg into a 500-foot-tall (150 m) giant who fights and kills Bender. Dial L for Leela – Farnsworth tells Leela that she will be named his successor because she is boring and predictable. Acting on impulse, Leela kills Farnsworth and the rest of the Planet Express crew except Fry, who she has sex with to keep him quiet. The Un-Freeze of a Lifetime – After just missing falling into the cryogenic tube, Fry discovers a wormhole that threatens to destroy the universe if Fry does not go to the future. Instead of stepping into the tube, Fry destroys it and brings about the end of the universe.',
                'delivery'  => null,
                'date'      => '2000-05-21',
                'viewers'   => null,
            ],
            '2ACV17' => [
                'subject'   => 'War Is the H-Word',
                'season'    => 2,
                'episode'   => 17,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Upon enrolling in the Earth army to get a military discount at the convenience store, Fry and Bender are whisked into war against a planet of ball-like aliens. Leela secretly enters the ranks to keep an eye on her friends.',
                'delivery'  => null,
                'date'      => '2000-11-26',
                'viewers'   => null,
            ],
            '2ACV18' => [
                'subject'   => 'The Honking',
                'season'    => 2,
                'episode'   => 18,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'At his late Uncle Vladimir\'s estate, Bender is run over by a vicious Werecar, inheriting the curse. The only way to stop Bender\'s fatal transformations is to seek out and destroy the original Werecar - the deadly Project Satan.',
                'delivery'  => null,
                'date'      => '2000-11-05',
                'viewers'   => null,
            ],
            '2ACV19' => [
                'subject'   => 'The Cryonic Woman',
                'season'    => 2,
                'episode'   => 19,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry, Leela, and Bender all lose their jobs at Planet Express after an accident, and they endeavor to return to their old jobs. Due to a mix-up of their career chips, Fry becomes a career counsellor at Applied Cryogenics, where he meets up with a familiar defrostee - his girlfriend from the 20th century, Michelle.',
                'delivery'  => null,
                'date'      => '2000-12-03',
                'viewers'   => null,
            ],
            '3ACV01' => [
                'subject'   => 'Amazon Women in the Mood',
                'season'    => 3,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A double-date for Kif, Amy, Zapp, and Leela ends in disaster when their orbiting restaurant crashes on planet Amazonia. The hulking female inhabitants of the planet take their male captives to the omniscient Femputer, who orders Fry, Zapp, and Kif to death by "snu-snu".',
                'delivery'  => '3ACV01',
                'date'      => '2001-02-04',
                'viewers'   => null,
            ],
            '3ACV02' => [
                'subject'   => 'Parasites Lost',
                'season'    => 3,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Fry becomes infested with parasitic worms that make him stronger and smarter, he finally finds the perfect way to profess his feelings to Leela. Meanwhile, the rest of the crew goes on a Fantastic Voyage-esque journey into Fry\'s body to eradicate the worms.',
                'delivery'  => '3ACV02',
                'date'      => '2001-01-21',
                'viewers'   => null,
            ],
            '3ACV03' => [
                'subject'   => 'A Tale of Two Santas',
                'season'    => 3,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A Planet Express mission to Robot Santa\'s colony on Neptune leaves the murderous robot trapped in the frozen sea, and Bender takes over as Santa, vowing to bring peace and goodwill to Xmas again. But when Bender is mistaken for the real Robot Santa, he is arrested and sentenced to death.',
                'delivery'  => null,
                'date'      => '2001-12-16',
                'viewers'   => null,
            ],
            '3ACV04' => [
                'subject'   => 'The Luck of the Fryrish',
                'season'    => 3,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry searches the ancient ruins of Old New York for his beloved lucky clover... only to conclude that his older brother stole not only his clover - but his life.',
                'delivery'  => null,
                'date'      => '2001-03-11',
                'viewers'   => null,
            ],
            '3ACV05' => [
                'subject'   => 'The Birdbot of Ice-Catraz',
                'season'    => 3,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A sober Bender crashes a dark matter tanker on Pluto, threatening the penguin reserve nearby. Leela helps out in the clean-up, but when the penguins begin mating out of control, drastic action must be taken to thin the herd.',
                'delivery'  => null,
                'date'      => '2001-03-04',
                'viewers'   => null,
            ],
            '3ACV06' => [
                'subject'   => 'Bendless Love',
                'season'    => 3,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender\'s urge to bend prompts Professor Farnsworth to send him on a rehabilitation visit to a steel factory, where he falls in love with a shapely fem-bot named Angleyne. But Bender\'s old rival Flexo and the intrusion of the Robot Mafia threaten to throw a wrench into the proceedings.',
                'delivery'  => null,
                'date'      => '2001-02-11',
                'viewers'   => null,
            ],
            '3ACV07' => [
                'subject'   => 'The Day the Earth Stood Stupid',
                'season'    => 3,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Earth is invaded by super-intelligent flying brains, who sap the Earth\'s populace of their intelligence. Leela is taken to Nibbler\'s home planet Eternia, where the Nibblonians explain that only one human is immune to the brains\' powers - Fry.',
                'delivery'  => null,
                'date'      => '2001-02-18',
                'viewers'   => null,
            ],
            '3ACV08' => [
                'subject'   => 'That\'s Lobstertainment!',
                'season'    => 3,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Dr. Zoidberg reunites with his uncle, silent hologram star Harold Zoid, and the two of them set out to make a movie together. They cast the temperamental Calculon in the lead role, who demands an Oscar for his performance, but the movie doesn\'t go over well with audiences.',
                'delivery'  => null,
                'date'      => '2001-02-25',
                'viewers'   => null,
            ],
            '3ACV09' => [
                'subject'   => 'The Cyber House Rules',
                'season'    => 3,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela meets up with her former orphanarium playmate Adlai Atkins, now a plastic surgeon, who offers to grant Leela surgery that will give her two eyes. Meanwhile, Bender adopts twelve orphans in order to collect $1200 in government stipends.',
                'delivery'  => null,
                'date'      => '2001-04-01',
                'viewers'   => null,
            ],
            '3ACV10' => [
                'subject'   => 'Where the Buggalo Roam',
                'season'    => 3,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Amy\'s parents\' ranch is hit by a dust storm that blows away their herd of buggalo, Kif sets out to prove his masculinity by rounding up the herd, only to become entangled with the native Martians.',
                'delivery'  => null,
                'date'      => '2002-03-03',
                'viewers'   => null,
            ],
            '3ACV11' => [
                'subject'   => 'Insane in the Mainframe',
                'season'    => 3,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Accused of robbing a bank, Fry and Bender plead insanity and are both sent to a robot insane asylum. While Bender and his buddy Roberto plan an escape, Fry is brainwashed into thinking that he is a robot.',
                'delivery'  => null,
                'date'      => '2001-04-08',
                'viewers'   => null,
            ],
            '3ACV12' => [
                'subject'   => 'The Route of All Evil',
                'season'    => 3,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Farnsworth\'s clone Cubert teams up with Hermes\' son Dwight to launch a newspaper delivery business. Farnsworth and Hermes scoff at the kids\' efforts - until the delivery boys accumulate enough capital to buy out Planet Express. Meanwhile, Fry and Leela use Bender to brew their own homemade beer.',
                'delivery'  => null,
                'date'      => '2002-12-08',
                'viewers'   => null,
            ],
            '3ACV13' => [
                'subject'   => 'Bendin\' in the Wind',
                'season'    => 3,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Bender is paralyzed in a tragic can opener accident, he discovers his musical washboard skills and goes on tour as a member of Beck\'s folk-rock band, acting as a voice for broken robots everywhere. Fry, Leela, Amy, and Zoidberg tag along in Fry\'s antique 1960s VW Van.',
                'delivery'  => null,
                'date'      => '2001-04-22',
                'viewers'   => null,
            ],
            '3ACV14' => [
                'subject'   => 'Time Keeps On Slippin\'',
                'season'    => 3,
                'episode'   => 14,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'While creating a team of mutants to play the Harlem Globetrotters, the Professor accidentally causes a disruption in time that threatens the existence of the universe. Meanwhile, Fry tries to win an unreceptive Leela\'s heart.',
                'delivery'  => null,
                'date'      => '2001-05-06',
                'viewers'   => null,
            ],
            '3ACV15' => [
                'subject'   => 'I Dated a Robot',
                'season'    => 3,
                'episode'   => 15,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry discovers the ability to download any celebrity onto a blank robot, and chooses to download Lucy Liu, with whom he falls madly in love. Repulsed by this disgusting display of human/robot love, Leela, Bender, and Zoidberg set out to shut down Nappster.com and put an end to illegal celebrity downloads forever.',
                'delivery'  => null,
                'date'      => '2001-05-13',
                'viewers'   => null,
            ],
            '3ACV16' => [
                'subject'   => 'A Leela of Her Own',
                'season'    => 3,
                'episode'   => 16,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela endeavors to become the first female blernsball player, but her lack of depth perception hinders her skills. Nevertheless, she becomes the pitcher for the New New York Mets, purely for her novelty value.',
                'delivery'  => null,
                'date'      => '2002-04-07',
                'viewers'   => null,
            ],
            '3ACV17' => [
                'subject'   => 'A Pharaoh to Remember',
                'season'    => 3,
                'episode'   => 17,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender fears that nobody will remember him after he dies, and sees his chance for immortality when the crew is enslaved on the planet Osiris 4. Posing as the planet\'s new pharaoh, Bender orders a humongous statue built in his honor, and quickly goes mad with power.',
                'delivery'  => null,
                'date'      => '2002-03-10',
                'viewers'   => null,
            ],
            '3ACV18' => [
                'subject'   => 'Anthology of Interest II',
                'season'    => 3,
                'episode'   => 18,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew ask three more questions for the What-If Machine: Bender asks what if he were human; Fry asks what if life were more like a video game; and Leela asks what if she found her true home. I, Meatbag – Farnsworth uses his latest invention to turn Bender into a human for the Nobel Committee. Bender goes on a week-long eating binge that turns him into a thousand-pound, morbidly obese blob and dies shortly after inspiring the committee to become party animals like he had. Raiders of the Lost Arcade – Earth is invaded by a race of aliens from Nintendu 64 in a manner similar to Space Invaders. Fry fails to stop the Nintendian invasion, but when their demands are revealed to be quarters for laundry, a compromise is reached allowing the Nintendians to mix their laundry with the Earthlings\' in exchange for the Earth\'s safety. Wizzin\' – Instead of seeing what would happen if she found her true home, Leela is knocked unconscious by the What-If Machine\'s lever and dreams herself in a parody of The Wizard of Oz with herself as Dorothy, Fry as the Scarecrow, Bender as the Tin Man, Zoidberg as the Cowardly Lion, Farnsworth as the Wizard, and Mom as the Wicked Witch. Leela decides she wants to become the new Wicked Witch instead of going home, but her reign of terror and her dream are cut short when Zoidberg splashes and melts her with water.',
                'delivery'  => null,
                'date'      => '2002-01-06',
                'viewers'   => null,
            ],
            '3ACV19' => [
                'subject'   => 'Roswell That Ends Well',
                'season'    => 3,
                'episode'   => 19,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry puts a metal popcorn pan in the microwave while a supernova occurs nearby, causing the radiation of both to rip open a time portal that sends the Planet Express crew back in time to Roswell, New Mexico in the year 1947. Bender\'s body is broken to pieces during the crash and Zoidberg is found by the U.S. military among the debris, and both are taken to Area 51 for experimentation. The crew requires another microwave to return to the 31st century, so they try to retrieve one without altering history. Meanwhile, Fry meets his grandfather Enos and begins fearing that he will cease to exist if his grandfather is killed. While trying to save him, however, Fry accidentally brings about Enos\' death when he takes him to a house which turns out to be in the middle of a nuclear testing range. However, Fry continues to exist due to either Enos getting Fry\'s grandmother pregnant before dying, or was an unrelated man whose surname happened to be "Fry". He ends up having sex with his grandmother. Since Fry has already changed history, the rest of the crew decides to launch an attack on Area 51, retrieving Bender, Zoidberg, and a microwave by force and allowing them to return through the closing time portal back to the 31st century.',
                'delivery'  => null,
                'date'      => '2001-12-09',
                'viewers'   => null,
            ],
            '3ACV20' => [
                'subject'   => 'Godfellas',
                'season'    => 3,
                'episode'   => 20,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender is accidentally shot out of the ship\'s torpedo tube and becomes lost in space. Floating through the ethereal darkness, Bender becomes inhabited with tiny alien life forms, but has trouble playing God to their unyielding prayers.',
                'delivery'  => null,
                'date'      => '2002-03-17',
                'viewers'   => null,
            ],
            '3ACV21' => [
                'subject'   => 'Future Stock',
                'season'    => 3,
                'episode'   => 21,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'With Planet Express in financial trouble, Fry nominates a flashy businessman from the 1980s to replace Professor Farnsworth as CEO of the company. That Guy goes on to sell Planet Express to Mom\'s Friendly Robot Corporation, putting everyone out of a job.',
                'delivery'  => null,
                'date'      => '2002-03-31',
                'viewers'   => null,
            ],
            '3ACV22' => [
                'subject'   => 'The 30% Iron Chef',
                'season'    => 3,
                'episode'   => 22,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender receives culinary lessons from the great chef Helmut Spargle, and puts his skills to the test on national television when he competes against Elzar on "Iron Cook". Meanwhile, Dr. Zoidberg accidentally destroys Professor Farnsworth\'s ship-in-a-bottle and pins the deed on Fry, only to be struck with remorse afterwards.',
                'delivery'  => null,
                'date'      => '2002-04-14',
                'viewers'   => null,
            ],
            '4ACV01' => [
                'subject'   => 'Kif Gets Knocked Up a Notch',
                'season'    => 4,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Amy\'s relationship with Kif leads to pregnancy... Kif being the pregnant one. But Amy fears that she will not be able to handle the burdens of motherhood, much to Kif\'s dismay.',
                'delivery'  => '4ACV01',
                'date'      => '2003-01-12',
                'viewers'   => null,
            ],
            '4ACV02' => [
                'subject'   => 'Leela\'s Homeworld',
                'season'    => 4,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Bender disposes nuclear waste in the sewers, the angry mutants drag him, Fry, and Leela down to the depths to be mutated. As they attempt to escape, Leela goes after two hooded figures who may hold the secret as to Leela\'s true heritage and the whereabouts of her parents.',
                'delivery'  => '4ACV02',
                'date'      => '2002-02-17',
                'viewers'   => null,
            ],
            '4ACV03' => [
                'subject'   => 'Love and Rocket',
                'season'    => 4,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After landing a major contract with a romance factory in Wisconsin and using the money to update the company, Bender falls deeply in love with the Planet Express ship autopilot\'s female voice setting (voiced by Sigourney Weaver). Meanwhile, Fry searches for the perfect candy heart to properly convey his feelings for Leela.',
                'delivery'  => '4ACV03',
                'date'      => '2002-02-10',
                'viewers'   => null,
            ],
            '4ACV04' => [
                'subject'   => 'Less Than Hero',
                'season'    => 4,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Dr. Zoidberg\'s mysterious miracle cream gives Fry and Leela superpowers. Teaming up with Bender, they form the New Justice Team, under the respective alter egos of Captain Yesterday, Clobberella, and Super King. But Leela\'s new duties as a superheroine put a strain on her relationship with her parents.',
                'delivery'  => null,
                'date'      => '2003-03-02',
                'viewers'   => null,
            ],
            '4ACV05' => [
                'subject'   => 'A Taste of Freedom',
                'season'    => 4,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'When Zoidberg publicly devours an Earth flag on Freedom Day, he is sentenced to death for his anarchic behavior. In protest, the Decapodians come to Zoidberg\'s aid by invading Earth, teaching the populace the true meaning of freedom.',
                'delivery'  => null,
                'date'      => '2002-12-22',
                'viewers'   => null,
            ],
            '4ACV06' => [
                'subject'   => 'Bender Should Not Be Allowed on TV',
                'season'    => 4,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => '"All My Circuits" holds an audition to replace the part of Calculon\'s son after the original actor has a literal breakdown on-set, and Bender lands the part, despite not being a child robot actor. Soon, Bender\'s uninhibited behavior proves to be a bad influence on children, and an outraged Bender leads a protest group to get himself banned from the airwaves.',
                'delivery'  => null,
                'date'      => '2003-08-03',
                'viewers'   => null,
            ],
            '4ACV07' => [
                'subject'   => 'Jurassic Bark',
                'season'    => 4,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry discovers his dog Seymour is being exhibited in a museum as a fossil, and takes it to Professor Farnsworth\'s lab to be revived. However, Bender is displeased with the lack of attention he has been receiving and becomes increasingly jealous of Seymour\'s fossil.',
                'delivery'  => null,
                'date'      => '2002-11-17',
                'viewers'   => null,
            ],
            '4ACV08' => [
                'subject'   => 'Crimes of the Hot',
                'season'    => 4,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The rapid increase in global warming is traced to a ventilation flaw that Professor Farnsworth failed to correct in his first robot prototype. As a result, all robots are ordered to be destroyed, but Bender refuses to go without a fight.',
                'delivery'  => null,
                'date'      => '2002-11-10',
                'viewers'   => null,
            ],
            '4ACV09' => [
                'subject'   => 'Teenage Mutant Leela\'s Hurdles',
                'season'    => 4,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The crew\'s attempts to de-age Professor Farnsworth result in everyone returning to their more youthful stages. While Farnsworth seeks out a way to re-age the crew, the newly-teenaged Leela takes the opportunity to experience the parental childhood she never had.',
                'delivery'  => null,
                'date'      => '2003-03-30',
                'viewers'   => null,
            ],
            '4ACV10' => [
                'subject'   => 'The Why of Fry',
                'season'    => 4,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Still unable to impress Leela, Fry sadly suspects that he has no importance in life - until Nibbler takes him on a mission to prevent the brains from destroying the universe. In the process, Fry learns what really happened when he was cryogenically frozen on December 31, 1999.',
                'delivery'  => null,
                'date'      => '2003-04-06',
                'viewers'   => null,
            ],
            '4ACV11' => [
                'subject'   => 'Where No Fan Has Gone Before',
                'season'    => 4,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry leads the crew on a quest across the galaxy to regain the forbidden 79 episodes of "Star Trek: The Original Series", where they encounter the original cast of the show - as well as their captor, an obsessive energy being named Melllvar.',
                'delivery'  => null,
                'date'      => '2002-04-21',
                'viewers'   => null,
            ],
            '4ACV12' => [
                'subject'   => 'The Sting',
                'season'    => 4,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A mission to collect honey from deadly space bees apparently leads to Fry\'s sting-induced death. Leela is wracked with remorse, until Fry visits her in her dreams. As Leela\'s hallucinations intensify, she begins to suspect that she might be going crazy.',
                'delivery'  => null,
                'date'      => '2003-06-01',
                'viewers'   => null,
            ],
            '4ACV13' => [
                'subject'   => 'Bend Her',
                'season'    => 4,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'In order to compete in the Fembot\'s division of the 3004 Olympics, Bender is surgically rebuilt to become a woman. Her trashy behavior catches the eye of Calculon, with whom she develops a strong and confusing celebrity relationship.',
                'delivery'  => null,
                'date'      => '2003-07-20',
                'viewers'   => null,
            ],
            '4ACV14' => [
                'subject'   => 'Obsoletely Fabulous',
                'season'    => 4,
                'episode'   => 14,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender is incompatible with Professor Farnsworth\'s new Robot 1-X, but rather than get an upgrade, Bender escapes to a desert island to start his life anew. There, he meets several other outdated robots and receives a downgrade, then leads his new comrades in a rebellion against technology.',
                'delivery'  => null,
                'date'      => '2003-07-27',
                'viewers'   => null,
            ],
            '4ACV15' => [
                'subject'   => 'The Farnsworth Parabox',
                'season'    => 4,
                'episode'   => 15,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Professor Farnsworth forbids the crew to look inside a mysterious box. Leela can\'t resist taking a peek, discovering the box to be a gateway to a parallel universe.',
                'delivery'  => null,
                'date'      => '2003-06-08',
                'viewers'   => null,
            ],
            '4ACV16' => [
                'subject'   => 'Three Hundred Big Boys',
                'season'    => 4,
                'episode'   => 16,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A $300 refund for all taxpayers results in a series of interconnected stories, following the Planet Express crew\'s endeavors to spend their money.',
                'delivery'  => null,
                'date'      => '2003-06-15',
                'viewers'   => null,
            ],
            '4ACV17' => [
                'subject'   => 'Spanish Fry',
                'season'    => 4,
                'episode'   => 17,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry is abducted by aliens, who harvest his nose as an aphrodisiac. The crew traces Fry\'s missing nose to Lrrr, leader of the Omicronians, who decides that Fry\'s "lower horn" would be a much better aphrodisiac to jump start his stagnant marriage with Ndnd.',
                'delivery'  => null,
                'date'      => '2003-07-13',
                'viewers'   => null,
            ],
            '4ACV18' => [
                'subject'   => 'The Devil\'s Hands Are Idle Playthings',
                'season'    => 4,
                'episode'   => 18,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Desperate to learn how to play the holophonor in order to impress Leela, Fry swaps hands with the Robot Devil. He goes on to become a skilled holophonor player, winning Leela\'s heart and penning an opera about her life story, but the Robot Devil still has a trick or two up his sleeve.',
                'delivery'  => null,
                'date'      => '2003-08-10',
                'viewers'   => null,
            ],
            '5ACV01' => [
                'subject'   => 'Bender\'s Big Score',
                'season'    => 5,
                'episode'   => 1,
                'duration'  => 88,
                'multipart' => true,
                'plot'      => 'As Fry learns he has a tattoo of Bender on his buttocks that has universal consequences, nudist alien scammers take over Planet Express and brainwash Bender via a virus to do their bidding. Meanwhile, Leela meets the man of her dreams and Hermes (literally) loses his head during a game of limbo.',
                'delivery'  => '5ACV01',
                'date'      => '2008-03-23',
                'viewers'   => null,
            ],
            '5ACV05' => [
                'subject'   => 'The Beast with a Billion Backs',
                'season'    => 5,
                'episode'   => 5,
                'duration'  => 90,
                'multipart' => true,
                'plot'      => 'When a mysterious tear appears in the universe, Professor Farnsworth mounts an expedition to explore whatever lies on the other side. Meanwhile, as Kif and Amy take their relationship to the next level, Fry discovers his new girlfriend may be spreading her love even further than he can handle.',
                'delivery'  => null,
                'date'      => '2008-10-19',
                'viewers'   => null,
            ],
            '5ACV09' => [
                'subject'   => 'Bender\'s Game',
                'season'    => 5,
                'episode'   => 9,
                'duration'  => 88,
                'multipart' => true,
                'plot'      => 'Leela\'s anger gets the better of her when she wastes precious gas and enters the Space Demolition Derby. In fact, it has gotten the better of her so many times that Farnsworth and Hermes decide to do something about it. Meanwhile, Bender becomes too obsessed with playing Dungeons and Dragons.',
                'delivery'  => null,
                'date'      => '2009-04-26',
                'viewers'   => null,
            ],
            '5ACV13' => [
                'subject'   => 'Into the Wild Green Yonder',
                'season'    => 5,
                'episode'   => 13,
                'duration'  => 89,
                'multipart' => true,
                'plot'      => 'The Planet Express crew visits Amy\'s parents, Leo and Inez, who are destroying the "old" Mars Vegas and constructing a more extravagant one. A group of eco-feminists, led by Frida Waterfall, protesting the destruction of the environment, leads to an accident wherein a piece of Frida\'s jewelry lodges inside Fry\'s brain granting him telepathy. The destruction upsets Leela, but Leo asserts that he has received environmental clearance — from a bribed Professor Farnsworth. Unconvinced, Leela saves a Martian muck leech, the last of its species, from the site.',
                'delivery'  => null,
                'date'      => '2009-08-30',
                'viewers'   => null,
            ],
            '6ACV01' => [
                'subject'   => 'Rebirth',
                'season'    => 6,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry finds his body covered in severe burns but cannot remember why. Professor Farnsworth reveals that the wormhole the Planet Express crew flew through to escape Zapp Brannigan led them back to Earth, where both ships crashed and killed everyone else. Farnsworth uses a birthing machine and resurrects everyone except Leela, who emerges in a supposedly irreversible coma. Devastated, Fry creates a robot replica of Leela with all her memories uploaded into it to continue their newfound relationship. However, the real Leela reawakens from her coma and gets into a fight with the robot Leela over Fry. Fry refuses to shoot either Leela when given the choice and accidentally shoots himself instead, and is revealed to be a robot as well. Farnsworth explains that the real Fry died protecting Leela in the crash and could not be resurrected in the then-incomplete birthing machine, so Leela made a robot replica of him that malfunctioned, killing her and leaving the robot Fry\'s body burned. Suddenly, the real Fry emerges from the birthing machine as it turns out the process was merely delayed for him. The robot Fry and Leela become a couple since they are already in love with each other, as do the real Fry and Leela, and the Planet Express crew celebrate their complete return.',
                'delivery'  => '6ACV01',
                'date'      => '2010-06-24',
                'viewers'   => '2.92',
            ],
            '6ACV02' => [
                'subject'   => 'In-A-Gadda-Da-Leela',
                'season'    => 6,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A rogue death sphere called the V-GINY, bent on censoring and destroying planets it deems to be indecent, is headed for Earth. Leela volunteers to destroy the death sphere, and reluctantly allows Zapp to come along. Their attack is disrupted and they crash land on an unknown planet similar to the Garden of Eden. Zapp shows his concerns for Leela\'s safety, causing Leela to gradually grow attracted to him. The two witness Earth\'s apparent destruction and decide to repopulate the human race à la Adam and Eve. At this point, however, Leela notices that some "fruit" Zapp had given her was actually trail mix Fry gave her, and Zapp confesses that everything was merely an elaborate scheme, including the Earth\'s destruction which was faked using a holographic projector from their ship, for her to think better of him and have sex with him. Furthermore, the two are actually on an island serving as the last unspoiled spot of nature on Earth. The V-GINY arrives at Earth and decides to spare it if "Adam" (Zapp) and "Eve" (Leela) have sexual intercourse, which they do.',
                'delivery'  => '6ACV02',
                'date'      => '2010-06-25',
                'viewers'   => '2.78',
            ],
            '6ACV03' => [
                'subject'   => 'Attack of the Killer App',
                'season'    => 6,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Everyone in New New York buys the latest, state of the art eyePhone, a device developed by Mom which is implanted in a person\'s eye that allows users to record videos and post them online. Fry and Bender challenge each other to see who can gain one million followers on their Twitcher accounts, with the loser having to dive into a pool of goat vomit and diarrhea. With Bender in the lead, Fry resorts to posting an embarrassing video of Leela revealing she has a singing boil on her rear named Susan, gaining him enough followers to end the bet with a tie. However, Leela is humiliated, so Fry posts a video of himself diving into the pool out of guilt, which everyone watches and causes them to forget about the video of Leela. Fry and Leela reconcile, completely unaware that Mom has infected all of Fry and Bender\'s followers with a virus that turns them into mindless zombies to make them buy more eyePhones.',
                'delivery'  => '6ACV03',
                'date'      => '2010-07-01',
                'viewers'   => '2.16',
            ],
            '6ACV04' => [
                'subject'   => 'Proposition Infinity',
                'season'    => 6,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Kif breaks up with Amy when she begins showing interest in "bad boys", which leads her to become attracted to Bender. The two engage in a secret robosexual relationship, a taboo romantic relationship between a robot and a human, much to the prejudice of Farnsworth since one of his girlfriends from his youth left him for a robot. With the support of the rest of the crew, Bender and Amy become engaged and hold a ballot proposition called Proposition Infinity, which they hope will lift the ban on robosexual marriage, with Farnsworth representing the opposing party. While arguing against Bender, Farnsworth suddenly remembers that his old girlfriend was also a robot. Not wanting to lose the debate after revealing he too was robosexual, Farnsworth has a change of heart and supports Proposition Infinity, which is passed as law and legalizes robosexual marriage. However, Bender leaves Amy and begins dating fembots again when he realizes that robosexual marriage is monogamous. Fortunately for Amy, she gets back together with Kif after discovering that he has by now adopted a "bad boy" attitude for her.',
                'delivery'  => '6ACV04',
                'date'      => '2010-07-08',
                'viewers'   => '2.01',
            ],
            '6ACV05' => [
                'subject'   => 'The Duh-Vinci Code',
                'season'    => 6,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry discovers a drawing of Leonardo da Vinci\'s fabled lost invention hidden in the inventor\'s beard that Farnsworth had kept. Farnsworth examines The Last Supper and discovers that the image of Saint James was painted over that of an ancient robot. The Planet Express crew go to Rome, enter Saint James\' crypt and find the robot, Animatronio, who seemingly dies before he can reveal any information. Further clues and investigation lead the crew to the Pantheon, where they uncover Leonardo\'s secret workshop filled with all his inventions. Animatronio suddenly reappears, having faked his death, and tries to kill the crew to keep the discovery a secret, but unwittingly reveals that all of Leonardo\'s inventions assemble to form a spacecraft. Fry and Farnsworth enter the craft and are taken to Vinci, a planet inhabited by humanoid intellectuals including Leonardo himself, who came to Earth as a means to escape being bullied as the stupidest among his peers, but became infuriated by how much more stupid its inhabitants were. Leonardo uses his missing drawing to build a giant machine designed to kill his tormentors, but Fry sabotages it before it can do so. As a last resort, Leonardo pulls a lever on the machine which drops a giant cog on him and crushes him to death. Fry and Farnsworth then take the spacecraft back to Earth.',
                'delivery'  => null,
                'date'      => '2010-07-15',
                'viewers'   => '2.2',
            ],
            '6ACV06' => [
                'subject'   => 'Lethal Inspection',
                'season'    => 6,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender learns that he was never given a backup unit that allows the memories of robots to be uploaded into new bodies when they die, meaning he is not immortal as he always thought. With the help of Hermes, Bender decides to confront his inspector from when he was first manufactured, known to him only as Inspector 5. All information on Inspector 5 turns up missing at the Central Bureaucracy where he worked, so Bender calls Mom\'s Friendly Robot Company to report his fatal defect. Not wanting a flawed robot to roam around in public, Mom sends killbots after Bender. Bender\'s and Hermes\' escape takes them to Tijuana, Mexico, where Bender was manufactured. Bender goes to Inspector 5\'s home to find once again that he is not there, and is forced to accept his own mortality. The killbots continue to try and kill Bender until Hermes fakes his death by accessing Inspector 5\'s database and labels Bender as "terminated", ending the pursuit. Bender returns home with Hermes with newfound pride in his mortality, oblivious as Hermes pulls out Inspector 5\'s missing profile and burns it, revealing himself as Inspector 5. A series of flashbacks then show how Hermes overrode the baby Bender\'s defect and quit his job because he could not bring himself to dispose of him, and how he kept all information on his identity a secret during the search with Bender.',
                'delivery'  => null,
                'date'      => '2010-07-22',
                'viewers'   => '1.92',
            ],
            '6ACV07' => [
                'subject'   => 'The Late Philip J. Fry',
                'season'    => 6,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry arrives late for lunch with Leela on her birthday and promises to make it up to her by taking her to Cavern on the Green that evening. Before he can leave to meet her for their date, Farnsworth forces him and Bender to test his new time machine by sending it forward in time by one minute, so Fry decides to make a birthday video recording card apologizing to Leela for being late again. Unfortunately, Farnsworth accidentally sets the machine to send them into the year 10,000 AD, and the card is lost in the time vortex outside. Because their time machine can only travel into the future, Fry, Bender, and Farnsworth continue traveling through time until a backwards time machine is invented. Meanwhile, Leela assumes Fry stood her up again to go a robot strip club which is reported to have been destroyed in an accident, leading her to believe he is dead until she reads his card, which reappears in the year 3050. Realizing Fry did not mean to stand her up, Leela goes back to Cavern on the Green and leaves a message in stalagmites for Fry to find, reading how she cherished their time together. Fry finds the message in the year One Billion, when all life is extinct. With no hope of finding another time machine, Fry, Bender, and Farnsworth watch the end of the universe together, which leads to a second Big Bang after which all time repeats itself. The time machine returns to the point in time before it first left, crushing the new universe\'s Fry, Bender, and Farnsworth beneath it in the process and allowing Fry to reach his date with Leela on time.',
                'delivery'  => null,
                'date'      => '2010-07-29',
                'viewers'   => '2.05',
            ],
            '6ACV08' => [
                'subject'   => 'That Darn Katz!',
                'season'    => 6,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Amy applies for her doctorate in applied physics by presenting a thesis for harnessing the power of Earth\'s rotation, but is interrupted due to her allergy to the committee chairman Professor Katz\'s cat and rejected. The cat follows the Planet Express crew home and summons a group of other cats in a flying saucer to brainwash the rest of the crew except for Amy and Nibbler, who discover Katz\'s cat to be the chairman\'s true identity and all cats to be a hyper-intelligent alien race from Thuban 9, a planet that lost its rotation and led to extreme temperatures on both eastern and western hemispheres. Katz uses Amy\'s thesis to build a machine powered by the brainwashed crew that transfers Earth\'s rotational energy to Thuban 9. Free from the cats\' control, the crew is unable to reverse the process when Amy suddenly realizes they can instead continue it until they restore the planet\'s rotation, albeit in the opposite direction. The plan is successful, once again halting the rotation of Thuban 9, and Amy receives her doctorate for her efforts.',
                'delivery'  => '6ACV08',
                'date'      => '2010-08-05',
                'viewers'   => '1.95',
            ],
            '6ACV09' => [
                'subject'   => 'A Clockwork Origin',
                'season'    => 6,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Farnsworth tries to prove the theory of evolution to creationist orangutan Dr. Banjo by discovering and presenting the final missing link between ape and man, but is dismissed when Banjo becomes the curator of the museum where Farnsworth displays his archeological find. Farnsworth decides he would rather not live on Earth anymore and has his crew relocate him to a distant, lifeless asteroid, using nanobots to turn the toxic minerals present into a habitable landscape. In mere hours, the nanobots evolve into robotic trilobites and devour the Planet Express ship. While trying to survive over the next few days, the crew witnesses the trilobites evolve into mechanical dinosaurs, which are wiped out by a solar flare and allow robotic mammals to evolve into modern-day humanoids. The crew is discovered by a robot naturalist who brings the discovery of intelligent, carbon-based life forms to the attention of the robot society, but Farnsworth is put on trial when he reveals the creationist-like fact that he is the source of their society. By the time the court reaches a verdict the next day, the robots have evolved into energy-based life forms and abandon the issue altogether, deeming it beneath them. The crew returns to Earth using a ship made from robot dinosaur parts and, in the wake of Farnsworth being the catalyst of robot evolution, Farnsworth and Dr. Banjo reconcile their differing beliefs.',
                'delivery'  => null,
                'date'      => '2010-08-12',
                'viewers'   => '1.96',
            ],
            '6ACV10' => [
                'subject'   => 'The Prisoner of Benda',
                'season'    => 6,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Farnsworth switches his body with Amy\'s using his latest invention so that he can relive his youth and Amy may gouge out like she always wanted. Upon doing so, they find they cannot switch their bodies back with each other, so they try to see if they can return to normal by switching their minds with the rest of the crew. Bender volunteers to switch minds with Farnsworth so he can use Amy\'s body to rob the yacht of Robohungarian emperor Nikolai, while Farnsworth joins a circus in Bender\'s body. Bender is caught by Nikolai, but tricks him into switching bodies with a robot bucket so he can live the life of an emperor. However, he discovers that Nikolai\'s wife and cousin are plotting to kill him, but is rescued by Farnsworth. Meanwhile, Leela switches bodies with Amy in Farnsworth\'s body when she comes to believe that Fry only loves her for her body, which leads to Fry switching bodies with Zoidberg to get back at her. To prove neither of them are shallow, the two go on a date and try to gross each other out until they ultimately make out and have sex. Amy, who switched bodies with Hermes after overeating in Leela\'s body so he may slim it back down, witnesses them making out and loses her appetite for good. In the end, everyone returns to their original bodies by adding the bodies of two Globetrotters who deduced the solution to the equation.',
                'delivery'  => null,
                'date'      => '2010-08-19',
                'viewers'   => '1.77',
            ],
            '6ACV11' => [
                'subject'   => 'Lrrreconcilable Ndndifferences',
                'season'    => 6,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Lrrr becomes unmotivated in his conquering of planets due to a midlife crisis. He tries to take over Earth to appease Ndnd, but fails when he arrives at Comic Con where he is mistaken for a costume contest participant, and is subsequently kicked out of his home by Ndnd. He moves into the Planet Express building and decides to take up dating again, falling in love with a female Omnicronian named Grrl until he discovers her to be a human in a costume who saw him at Comic Con. Lrrr decides to follow Leela\'s advice to win back Ndnd by staging an invasion of Earth broadcast by Orson Welles à la the 1938 "War of the Worlds" broadcast, which not only fools Ndnd, but also tricks Earth into actually surrendering. Leela scolds Lrrr and demands he tell Ndnd the truth, but he is unable to due to her renewed romantic interest in him. Lrrr is later caught with Leela by the suspicious Ndnd and admits he faked the invasion and had an affair with Grrl, but Ndnd disregards those facts as she is only upset that he is letting Leela nag him. Lrrr is forced to prove his love to Ndnd by shooting Leela with a disintegration ray, but as he fires, Fry takes the shot and sacrifices himself to protect Leela. Ndnd gets back together with Lrrr since he was at least willing to shoot Leela, and Fry is found to be alive since the "disintegration ray" turns out to be a novelty teleportation ray.',
                'delivery'  => null,
                'date'      => '2010-08-26',
                'viewers'   => '1.98',
            ],
            '6ACV12' => [
                'subject'   => 'The Mutants Are Revolting',
                'season'    => 6,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After making their 100th delivery, for which Bender organizes a huge party, the Planet Express crew is invited to a fund raiser for giving sewer mutants the needed donations to keep them away from the surface. Seeing how upset Leela is by this, Fry publicly lets slip that she is a mutant living illegally on the surface, causing her to be banished to the sewers for life. Feeling guilty for ruining Leela\'s life, Fry and the Planet Express crew appeal to the mayor to let her live on the surface again, but they are instead banished to the sewers for two weeks for knowingly harboring a mutant on the surface, minus Bender, who is enjoying the party. To appease Leela\'s anger and understand the life of a mutant, Fry jumps into a pool of toxic waste and emerges as a hideous blob. Leela is moved by Fry\'s sacrifice and decides to lead a mutant rebellion against the surface people by getting Bender, who ends up stopping the party after realizing it is no fun without the rest of the crew, to bend the giant sewer pipes together, thus backing up all the pipes around the city and flooding the surface with sewage. The mayor complies to the mutants\' demands for equal rights, allowing them to finally live on the surface. Furthermore, it is revealed that Fry had not mutated, but was merely stuck in the body of another mutant. Afterwards, the Planet Express crew has a second "100th Delivery" party since they missed out on the first.',
                'delivery'  => null,
                'date'      => '2010-09-02',
                'viewers'   => '1.79',
            ],
            '6ACV13' => [
                'subject'   => 'The Futurama Holiday Spectacular',
                'season'    => 6,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'A holiday special featuring three holiday-themed segments, following the same structure as the episodes "Anthology of Interest I" and "Anthology of Interest II." The three segments are based around the three winter holidays previously featured on Futurama: Xmas, Kwanzaa and Robanukah.',
                'delivery'  => null,
                'date'      => '2010-11-21',
                'viewers'   => '1.3',
            ],
            '6ACV14' => [
                'subject'   => 'The Silence of the Clamps',
                'season'    => 6,
                'episode'   => 14,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender goes into witness relocation after testifying against the robot Mafia.',
                'delivery'  => null,
                'date'      => '2011-07-14',
                'viewers'   => '1.41',
            ],
            '6ACV15' => [
                'subject'   => 'Möbius Dick',
                'season'    => 6,
                'episode'   => 15,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'While picking up a memorial statue of Planet Express\'s first crew, Leela becomes dangerously obsessed with catching a space whale, à la Captain Ahab from Moby Dick.',
                'delivery'  => null,
                'date'      => '2011-08-04',
                'viewers'   => '1.46',
            ],
            '6ACV16' => [
                'subject'   => 'Law and Oracle',
                'season'    => 6,
                'episode'   => 16,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fed up with his go-nowhere job, Fry joins the police force and is promoted into the Future Crimes Division, where he must choose between his job and his friend, Bender, after seeing him commit a future crime. Meanwhile, the Planet Express office gets a lot less funny during Fry\'s departure.',
                'delivery'  => null,
                'date'      => '2011-07-07',
                'viewers'   => '1.55',
            ],
            '6ACV17' => [
                'subject'   => 'Benderama',
                'season'    => 6,
                'episode'   => 17,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender creates duplicates of himself to get out of doing work, but the clones end up replicating and causing mass intoxication when they reach atomic level and manipulate the water molecules into alcohol. Meanwhile, an ugly space giant (voiced by Patton Oswalt) invades Earth.',
                'delivery'  => null,
                'date'      => '2011-06-23',
                'viewers'   => '2.47',
            ],
            '6ACV18' => [
                'subject'   => 'The Tip of the Zoidberg',
                'season'    => 6,
                'episode'   => 18,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew learn the truth about Farnsworth and Zoidberg\'s pasts.',
                'delivery'  => null,
                'date'      => '2011-08-18',
                'viewers'   => '1.38',
            ],
            '6ACV19' => [
                'subject'   => 'Ghost in the Machines',
                'season'    => 6,
                'episode'   => 19,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender commits suicide (actually gets murdered, since the suicide booth is still bitter over Bender dumping her) after Fry saves a human during the Parade Day parade instead of a robot, and the only way out of being a wandering spirit is to scare Fry to death by haunting him.',
                'delivery'  => null,
                'date'      => '2011-06-30',
                'viewers'   => '1.92',
            ],
            '6ACV20' => [
                'subject'   => 'Neutopia',
                'season'    => 6,
                'episode'   => 20,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Gender roles and sexuality get turned on their heads when an alien traps the men and women of Planet Express on an abandoned planet and conducts experiments on how men and women interact.',
                'delivery'  => null,
                'date'      => '2011-06-24',
                'viewers'   => '2.5',
            ],
            '6ACV21' => [
                'subject'   => 'Yo Leela Leela',
                'season'    => 6,
                'episode'   => 21,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After failing at telling a story to the children at her old orphanarium, Leela comes up with a new story about weird aliens who laugh, sing, and learn important life lessons, which makes her the creator of the Tickelodeon Network\'s newest show -- until Bender learns the secret behind Leela\'s success.',
                'delivery'  => null,
                'date'      => '2011-07-21',
                'viewers'   => '1.41',
            ],
            '6ACV22' => [
                'subject'   => 'Fry Am the Egg Man',
                'season'    => 6,
                'episode'   => 22,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Disgusted over the state of fast food, Leela decides to put everyone on an organic diet, and Fry nutures a farmer\'s market egg housing a bone vampire.',
                'delivery'  => null,
                'date'      => '2011-08-11',
                'viewers'   => '1.46',
            ],
            '6ACV23' => [
                'subject'   => 'All the Presidents\' Heads',
                'season'    => 6,
                'episode'   => 23,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry takes a second job as a head museum janitor, and a late night party leads to a trip back in time where the Planet Express crew\'s meddling leads to the British defeating America during the American Revolution.',
                'delivery'  => null,
                'date'      => '2011-07-28',
                'viewers'   => '1.49',
            ],
            '6ACV24' => [
                'subject'   => 'Cold Warriors',
                'season'    => 6,
                'episode'   => 24,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry inadvertently reintroduces the common cold (which died out 500 years before Fry was revived) to the 31st century, while flashbacks tell the story of how Fry adopted a guinea pig for a school science project.',
                'delivery'  => null,
                'date'      => '2011-08-25',
                'viewers'   => '1.52',
            ],
            '6ACV25' => [
                'subject'   => 'Overclockwise',
                'season'    => 6,
                'episode'   => 25,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender is overclocked by Cubert, gradually becoming more powerful in computing ability, until eventually becoming omniscient and clairvoyant. Meanwhile, Fry and Leela worry about their future together and Mom sues Farnsworth over abusing Bender\'s warranty.',
                'delivery'  => null,
                'date'      => '2011-09-01',
                'viewers'   => '1.57',
            ],
            '6ACV26' => [
                'subject'   => 'Reincarnation',
                'season'    => 6,
                'episode'   => 26,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'An episode featuring Futurama animated in three retro styles: "Colorama" has Futurama as a black-and-white, rubber-hose cartoon from the 1930s, "Future Challenge 3000" has the show as an early 1990s low-resolution video game, and Action Delivery Force has the show as a 1980s Japanese cartoon.',
                'delivery'  => null,
                'date'      => '2011-09-08',
                'viewers'   => '1.48',
            ],
            '7ACV01' => [
                'subject'   => 'The Bots and the Bees',
                'season'    => 7,
                'episode'   => 1,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender\'s fight with the new soda machine, Bev (voiced by Wanda Sykes) leads to anger-fueled sexual intercourse -- and a son.',
                'delivery'  => null,
                'date'      => '2012-06-20',
                'viewers'   => '1.57',
            ],
            '7ACV02' => [
                'subject'   => 'A Farewell to Arms',
                'season'    => 7,
                'episode'   => 2,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew uncovers an ancient Martian calendar (that looks like the one the Mayans created) that predicts that the world will come to an end in the year 3012. Meanwhile, Fry\'s good-intentioned acts of kindness to Leela end in disaster.',
                'delivery'  => null,
                'date'      => '2012-06-21',
                'viewers'   => '1.65',
            ],
            '7ACV03' => [
                'subject'   => 'Decision 3012',
                'season'    => 7,
                'episode'   => 3,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The head of Nixon runs for re-re-election against a competent politician who is accused of being an alien when his Earth birth certificate cannot be found.',
                'delivery'  => null,
                'date'      => '2012-06-27',
                'viewers'   => '1.45',
            ],
            '7ACV04' => [
                'subject'   => 'The Thief of Baghead',
                'season'    => 7,
                'episode'   => 4,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender joins the paparazzi and attempts to photograph a famous actor whose face has to be hidden for the good of mankind.',
                'delivery'  => null,
                'date'      => '2012-07-04',
                'viewers'   => '1.07',
            ],
            '7ACV05' => [
                'subject'   => 'Zapp Dingbat',
                'season'    => 7,
                'episode'   => 5,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela is horrified when her mother divorces her father -- and begins dating Zapp Brannigan.',
                'delivery'  => null,
                'date'      => '2012-07-11',
                'viewers'   => '1.1',
            ],
            '7ACV06' => [
                'subject'   => 'The Butterjunk Effect',
                'season'    => 7,
                'episode'   => 6,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela and Amy volunteer to be players in the brutal, redneck sport of Butterfly Derby and get hooked on a performance enhancer made from butterfly hormones.',
                'delivery'  => null,
                'date'      => '2012-07-18',
                'viewers'   => '1.19',
            ],
            '7ACV07' => [
                'subject'   => 'The Six Million Dollar Mon',
                'season'    => 7,
                'episode'   => 7,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After firing himself from at Planet Express for being useless, Hermes replaces parts of his body with robotic counterparts to increase his productivity.',
                'delivery'  => null,
                'date'      => '2012-07-25',
                'viewers'   => '1.19',
            ],
            '7ACV08' => [
                'subject'   => 'Fun on a Bun',
                'season'    => 7,
                'episode'   => 8,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry\'s drunken antics at an Oktoberfest (which, in 1000 years\' time, has become a sophisticated affair rather than an excuse to get drunk on German beer) land him in a civilization of Neanderthals, while everyone else believes that Fry died in a sausage-making accident.',
                'delivery'  => null,
                'date'      => '2012-08-01',
                'viewers'   => '1.01',
            ],
            '7ACV09' => [
                'subject'   => 'Free Will Hunting',
                'season'    => 7,
                'episode'   => 9,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'After going to college and turning to a life of crime to pay off a debt to the Robot Mafia, Bender discovers that, because he\'s a robot, he has no free will and sets out on a journey of being an independent thinker.',
                'delivery'  => null,
                'date'      => '2012-08-08',
                'viewers'   => '0.99',
            ],
            '7ACV10' => [
                'subject'   => 'Near-Death Wish',
                'season'    => 7,
                'episode'   => 10,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Professor is deeply disturbed when Fry reunites him with his long-lost parents, whom he blames for not spending time with him.',
                'delivery'  => null,
                'date'      => '2012-08-15',
                'viewers'   => '1.18',
            ],
            '7ACV11' => [
                'subject'   => '31st Century Fox',
                'season'    => 7,
                'episode'   => 11,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender fights for the rights of robot foxes after finding out that robot foxes are being hunted for sport.',
                'delivery'  => null,
                'date'      => '2012-08-29',
                'viewers'   => '1.35',
            ],
            '7ACV12' => [
                'subject'   => 'Viva Mars Vegas',
                'season'    => 7,
                'episode'   => 12,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The crew stages a casino heist to recover stolen property from the robot Mafia, while Zoidberg finds a bag of ill-gotten cash in the Dumpster and blows it all at the casino.',
                'delivery'  => null,
                'date'      => '2012-08-22',
                'viewers'   => '1.07',
            ],
            '7ACV13' => [
                'subject'   => 'Naturama',
                'season'    => 7,
                'episode'   => 13,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Futurama characters are depicted as animals in a three-part documentary episode modeled after Mutual of Omaha\'s Wild Kingdom.',
                'delivery'  => null,
                'date'      => '2012-08-30',
                'viewers'   => '1.36',
            ],
            '7ACV14' => [
                'subject'   => 'Forty Percent Leadbelly',
                'season'    => 7,
                'episode'   => 14,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender meets his hero, Silicon Red, a folk singer who has been in jail 30 times, during a convict transport, and uses a wireless 3D printer to duplicate his guitar, but the wireless connection between Bender\'s brain and the 3D printer turns his folk song about an angry space railbot hunting down Bender into a reality.',
                'delivery'  => null,
                'date'      => '2013-07-03',
                'viewers'   => '0.81',
            ],
            '7ACV15' => [
                'subject'   => '2-D Blacktop',
                'season'    => 7,
                'episode'   => 15,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Professor Farnsworth joins a gang of street racing punks, and ends up in a two-dimensional world.',
                'delivery'  => null,
                'date'      => '2013-06-19',
                'viewers'   => '1.4',
            ],
            '7ACV16' => [
                'subject'   => 'T.: The Terrestrial',
                'season'    => 7,
                'episode'   => 16,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'In a reverse parody of E.T.: The Extraterrestrial, Fry gets left behind on Omicron Persei 8 (which has blocked off all trade and communication with Earth) after the Planet Express crew sneak onto the planet to gather a marijuana-esque herb needed for the Professor\'s tea.',
                'delivery'  => null,
                'date'      => '2013-06-26',
                'viewers'   => '1.02',
            ],
            '7ACV17' => [
                'subject'   => 'Fry and Leela\'s Big Fling',
                'season'    => 7,
                'episode'   => 17,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Fry and Leela\'s romantic vacation goes south when Leela\'s prior boyfriend, Sean (who before, this episode, has been mentioned, but not seen), drops by. Amy, Bender and Zoidberg have to rescue Fry and Leela from their vacation spot (which is an intergalactic zoo).',
                'delivery'  => null,
                'date'      => '2013-06-20',
                'viewers'   => '1.49',
            ],
            '7ACV18' => [
                'subject'   => 'The Inhuman Torch',
                'season'    => 7,
                'episode'   => 18,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender becomes a firefighter, and ends up housing a solar flare who wants to blow up the Earth from the inside.',
                'delivery'  => null,
                'date'      => '2013-07-10',
                'viewers'   => '1.43',
            ],
            '7ACV19' => [
                'subject'   => 'Saturday Morning Fun Pit',
                'season'    => 7,
                'episode'   => 19,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Amid angry protests from anti-TV violence groups on the White House lawn, the head of Richard Nixon and the headless body of Spiro Agnew try to watch a Saturday morning cartoon block, featuring the Futurama gang in parodies of Saturday morning favorites from the late 1970s into the early-to-mid 1980s: A Scooby-Doo parody called Bendee Boo and the Mystery Crew featuring appearances by Larry Bird, George Takei, and the Harlem Globetrotters; a Strawberry Shortcake-meets-The Smurfs parody called Purpleberry Pond that was only made to advertise an excessively sugary cereal; and a violent G.I. Joe parody called G.I. Zapp that Nixon tries to edit for violence and offensive language.',
                'delivery'  => null,
                'date'      => '2013-07-17',
                'viewers'   => '1.13',
            ],
            '7ACV20' => [
                'subject'   => 'Calculon 2.0',
                'season'    => 7,
                'episode'   => 20,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Calculon (who died in "Thief of Baghead") is backed up and put into the body of a new robot so he can return to All My Circuits, only to learn that his over-the-top acting was never appreciated.',
                'delivery'  => null,
                'date'      => '2013-07-24',
                'viewers'   => '1.23',
            ],
            '7ACV21' => [
                'subject'   => 'Assie Come Home',
                'season'    => 7,
                'episode'   => 21,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Bender searches the universe for his shiny, metal ass after an alien street gang has him stripped down to his bulb eyes and mouth grille.',
                'delivery'  => null,
                'date'      => '2013-07-31',
                'viewers'   => '1.19',
            ],
            '7ACV22' => [
                'subject'   => 'Leela and the Genestalk',
                'season'    => 7,
                'episode'   => 22,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Leela becomes mutated and is captured by Mom of Mom\'s Friendly Robot Company.[21]',
                'delivery'  => null,
                'date'      => '2013-08-07',
                'viewers'   => '1.36',
            ],
            '7ACV23' => [
                'subject'   => 'Game of Tones',
                'season'    => 7,
                'episode'   => 23,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The Planet Express crew enter Fry\'s dreams and find themselves back in the year 1999 in search of a mysterious alien song.',
                'delivery'  => null,
                'date'      => '2013-08-14',
                'viewers'   => '1.07',
            ],
            '7ACV24' => [
                'subject'   => 'Murder on the Planet Express',
                'season'    => 7,
                'episode'   => 24,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'The crew get trapped aboard the Planet Express ship with a horrific alien creature.',
                'delivery'  => null,
                'date'      => '2013-08-21',
                'viewers'   => '1.04',
            ],
            '7ACV25' => [
                'subject'   => 'Stench and Stenchibility',
                'season'    => 7,
                'episode'   => 25,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Zoidberg falls for a flower vendor, who has no sense of smell, while Bender competes against a cute little girl in a tap dancing competition.',
                'delivery'  => null,
                'date'      => '2013-08-28',
                'viewers'   => '1.4',
            ],
            '7ACV26' => [
                'subject'   => 'Meanwhile',
                'season'    => 7,
                'episode'   => 26,
                'duration'  => 22,
                'multipart' => false,
                'plot'      => 'Professor Farnsworth invents a button that can take a person 10 seconds back in time, which complicates Fry\'s plans to finally marry Leela.[26][27]',
                'delivery'  => null,
                'date'      => '2013-09-04',
                'viewers'   => '2.21',
            ],
        ];

        $state_produced = $this->getReference('state:produced');
        $state_released = $this->getReference('state:released');

        foreach ($records as $code => $info) {

            $record = new Record();

            /** @noinspection PhpParamsInspection */
            $record
                ->setSubject($info['subject'])
                ->setCreatedAt(strtotime($info['date'] . ' 09:00:00'))
                ->setChangedAt(strtotime($info['date'] . ' 09:00:01'))
                ->setClosedAt(strtotime($info['date'] . ' 09:00:01'))
                ->setResumedAt(0)
                ->setState($state_released)
                ->setAuthor($this->getReference('user:artem'))
                ->setResponsible(null)
            ;

            $event = new Event();

            $event
                ->setType(Event::RECORD_CREATED)
                ->setCreatedAt($record->getCreatedAt())
                ->setParameter($state_produced->getId())
                ->setRecord($record)
                ->setUser($record->getAuthor())
            ;

            $event2 = new Event();

            $event2
                ->setType(Event::STATE_CHANGED)
                ->setCreatedAt($record->getClosedAt())
                ->setParameter($state_released->getId())
                ->setRecord($record)
                ->setUser($record->getAuthor())
            ;

            $manager->persist($record);
            $manager->persist($event);
            $manager->persist($event2);
            $manager->flush();

            $decimal_value_id = null;

            if ($info['viewers']) {

                $reference = 'value:decimal:' . $info['viewers'];

                if ($this->hasReference($reference)) {
                    $decimal_value = $this->getReference($reference);
                }
                else {
                    $decimal_value = new DecimalValue();
                    $decimal_value->setValue($info['viewers']);
                    $this->addReference($reference, $decimal_value);
                    $manager->persist($decimal_value);
                    $manager->flush();
                }

                $decimal_value_id = $decimal_value->getId();
            }

            $string_value = new StringValue();
            $string_value->setToken(md5($code));
            $string_value->setValue($code);

            $text_value = new TextValue();
            $text_value->setToken(md5($info['plot']));
            $text_value->setValue($info['plot']);

            $manager->persist($string_value);
            $manager->persist($text_value);
            $manager->flush();

            $fields = [
                'state:produced:1' => $info['season'],
                'state:produced:2' => $info['episode'],
                'state:produced:3' => $string_value->getId(),
                'state:produced:4' => $info['duration'],
                'state:produced:5' => $info['multipart'],
                'state:produced:6' => $text_value->getId(),
                'state:released:1' => strtotime($info['date']),
                'state:released:2' => $decimal_value_id,
            ];

            foreach ($fields as $field_ref => $field_value) {

                /** @var Field $field */
                $field = $this->getReference($field_ref);

                $value = new FieldValue();

                /** @noinspection PhpParamsInspection */
                $value
                    ->setEventId($event->getId())
                    ->setFieldId($field->getId())
                    ->setType($field->getType())
                    ->setValueId($field_value)
                    ->setCurrent(true)
                    ->setEvent(substr($field_ref, 0, 14) == 'state:produced' ? $event : $event2)
                    ->setField($field)
                ;

                $manager->persist($value);
            }

            $read = $this->container->get('doctrine')->getRepository('eTraxis:LastRead')->findOneBy([
                'recordId' => $record->getId(),
                'userId'   => $event->getUser()->getId(),
            ]);

            if (!$read) {

                $read = new LastRead();

                $read
                    ->setRecordId($record->getId())
                    ->setUserId($event->getUser()->getId())
                    ->setRecord($record)
                    ->setUser($event->getUser())
                ;
            }

            $read->setReadAt($event2->getCreatedAt());

            $manager->persist($read);
            $manager->flush();
        }
    }
}
