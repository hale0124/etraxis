<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Model\Event;
use eTraxis\Model\Field;
use eTraxis\Model\FieldValue;
use eTraxis\Model\Issue;
use eTraxis\Model\LastRead;
use eTraxis\Model\StringValue;
use eTraxis\Model\TextValue;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadIssuesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issues = [
            [
                'subject'     => 'Prizes for the claw crane',
                'assignee'    => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Sal',
                'delivery_at' => 'Luna Park, Moon',
                'notes'       => null,
                'date'        => '1999-04-04',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Lug nuts',
                'assignee'    => 'user:bender',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robots of Chapek 9',
                'delivery_at' => 'Chapek 9',
                'notes'       => 'Only Bender goes on the planet, because humans would be killed.',
                'date'        => '1999-04-20',
                'notes2'      => null,
            ],
            [
                'subject'     => 'A sign saying "Please Don\'t Drink the Emperor"',
                'assignee'    => 'user:leela',
                'crew'        => 'Amy, Bender, Fry, Leela, Zoidberg',
                'delivery_to' => 'Emperor Bont',
                'delivery_at' => 'Trisol',
                'notes'       => null,
                'date'        => '1999-05-04',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Subpoenas',
                'assignee'    => 'user:leela',
                'crew'        => 'Fry, Leela, Bender',
                'delivery_to' => 'Possibly Big Vinnie',
                'delivery_at' => 'Sicily 8',
                'notes'       => null,
                'date'        => '1999-05-18',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Guenter',
                'assignee'    => 'user:hubert',
                'crew'        => 'Bender, Fry, Leela, Prof. Farnsworth',
                'delivery_to' => 'Prof. Farnsworth',
                'delivery_at' => 'Mars University',
                'notes'       => 'Delivery to the Professor\'s office at Mars University',
                'date'        => '1999-10-03',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Ceremonial oversized Scissors',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'DOOP',
                'delivery_at' => 'DOOP headquarters',
                'notes'       => null,
                'date'        => '1999-11-28',
                'notes2'      => 'Delivery intercepted by Zapp Brannigan',
            ],
            [
                'subject'     => 'Pillows',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Hotel management',
                'delivery_at' => 'Hotel, Stumbos 4',
                'notes'       => null,
                'date'        => '1999-12-29',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Atom of jumbonium for the Miss Universe Pageant',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Bob Barker\'s head',
                'delivery_at' => 'Tova 9',
                'notes'       => null,
                'date'        => '2000-02-20',
                'notes2'      => 'Delivery disrupted by Bender\'s theft of the atom.',
            ],
            [
                'subject'     => 'Popcorn',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'People of Cineplex 14',
                'delivery_at' => 'Cineplex 14',
                'notes'       => null,
                'date'        => '2000-03-06',
                'notes2'      => 'Delivery aborted when Leela received an email from Alcazar',
            ],
            [
                'subject'     => 'Letters for Santa',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Robot Santa Claus',
                'delivery_at' => 'Neptune',
                'notes'       => null,
                'date'        => '2001-12-23',
                'notes2'      => null,
            ],
            [
                'subject'     => 'A sandstone block',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Osirians',
                'delivery_at' => 'Osiris 4',
                'notes'       => null,
                'date'        => '2002-03-10',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Medication',
                'assignee'    => 'user:amy',
                'crew'        => 'Amy, Bender, Fry, Leela',
                'delivery_to' => 'Hive mind of Nigel 7',
                'delivery_at' => 'Nigel 7',
                'notes'       => null,
                'date'        => '2003-01-12',
                'notes2'      => 'Delivery failed due to Amy Wong commandeering the Planet Express ship.',
            ],
            [
                'subject'     => 'Candy hearts',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Lrrr and Ndnd',
                'delivery_at' => 'Omicron Persei 8',
                'notes'       => null,
                'date'        => '2002-02-10',
                'notes2'      => 'Delivery aborted after Omicronians began attacking the crew. Hearts dumped into quasar.',
            ],
            [
                'subject'     => 'Ice from Halley\'s Comet',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'The people of Earth',
                'delivery_at' => 'Earth',
                'notes'       => null,
                'date'        => '2002-11-10',
                'notes2'      => 'Delivery failed due to the comet running out of ice.',
            ],
            [
                'subject'     => 'Barstool softener',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Nude Bartender',
                'delivery_at' => 'Planet XXX',
                'notes'       => null,
                'date'        => '2007-11-27',
                'notes2'      => null,
            ],
            [
                'subject'     => 'Billion-mile security fence',
                'assignee'    => 'user:hubert',
                'crew'        => 'Hermes, Prof. Farnsworth, Scruffy, Zoidberg',
                'delivery_to' => 'Leo Wong',
                'delivery_at' => 'Deep Space',
                'notes'       => null,
                'date'        => '2009-02-23',
                'notes2'      => 'Delivery intercepted by Feministas',
            ],
            [
                'subject'     => 'e-Waste',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Antarian workers',
                'delivery_at' => 'Third World of the Antares system',
                'notes'       => null,
                'date'        => '2010-07-01',
                'notes2'      => null,
            ],
            [
                'subject'     => 'A soufflé laced with nitroglycerine',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Hermes, Fry, Leela, Prof. Farnsworth, Zoidberg',
                'delivery_to' => 'Mrs. Astor',
                'delivery_at' => 'Waldorf Asteroid',
                'notes'       => null,
                'date'        => '2010-09-02',
                'notes2'      => null,
            ],
            [
                'subject'     => 'New clamps for Francis X. Clampazzo.',
                'assignee'    => 'user:leela',
                'crew'        => 'Bender, Fry, Leela',
                'delivery_to' => 'Francis X. Clampazzo',
                'delivery_at' => 'The Donbot\'s mansion, Long Long Island',
                'notes'       => null,
                'date'        => '2010-11-21',
                'notes2'      => null,
            ],
            [
                'subject'     => '200 feet of hanging rope for the hanging of multiheaded monster.',
                'assignee'    => 'user:hubert',
                'crew'        => 'Amy, Bender, Fry, Hermes, Leela, the Professor, Zoidberg',
                'delivery_to' => 'Sheriff Burley',
                'delivery_at' => 'Aldrin\'s Gulch Town Jail, Aldrin\'s Gulch, Moon',
                'notes'       => null,
                'date'        => '2011-07-14',
                'notes2'      => null,
            ],
            [
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

        foreach ($issues as $info) {

            $issue = new Issue();

            /** @noinspection PhpParamsInspection */
            $issue
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
                ->setType(Event::ISSUE_CREATED)
                ->setCreatedAt($issue->getCreatedAt())
                ->setParameter($state_new->getId())
                ->setIssue($issue)
                ->setUser($issue->getAuthor())
            ;

            $event2 = new Event();

            $event2
                ->setType(Event::ISSUE_ASSIGNED)
                ->setCreatedAt($issue->getCreatedAt())
                ->setParameter($issue->getResponsible()->getId())
                ->setIssue($issue)
                ->setUser($issue->getAuthor())
            ;

            $manager->persist($issue);
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
                ->setIssueId($issue->getId())
                ->setUserId($issue->getAuthor()->getId())
                ->setReadAt($issue->getCreatedAt())
                ->setIssue($issue)
                ->setUser($issue->getAuthor())
            ;

            $manager->persist($field);
            $manager->persist($read);

            if ($info['date'] < '2010-01-01') {

                $event = new Event();

                $event
                    ->setType(Event::STATE_CHANGED)
                    ->setCreatedAt(strtotime($info['date'] . ' 17:00:00'))
                    ->setParameter($state_delivered->getId())
                    ->setIssue($issue)
                    ->setUser($issue->getResponsible())
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
                $issue
                    ->setClosedAt($event->getCreatedAt())
                    ->setState($state_delivered)
                    ->setResponsible(null)
                ;

                $read = $this->container->get('doctrine')->getRepository('eTraxis:LastRead')->findOneBy([
                    'issueId' => $issue->getId(),
                    'userId'  => $event->getUser()->getId(),
                ]);

                if (!$read) {

                    $read = new LastRead();

                    $read
                        ->setIssueId($issue->getId())
                        ->setUserId($event->getUser()->getId())
                        ->setIssue($issue)
                        ->setUser($event->getUser())
                    ;
                }

                $read->setReadAt($event->getCreatedAt());

                $manager->persist($field);
                $manager->persist($issue);
                $manager->persist($read);
            }
        }

        $manager->flush();
    }
}
