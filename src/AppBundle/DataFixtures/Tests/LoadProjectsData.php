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
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $projects = [
            'eTraxis 1.0' => '2006-12-22',
            'eTraxis 2.0' => '2009-12-04',
            'eTraxis 3.0' => '2010-10-24',
        ];

        foreach ($projects as $name => $date) {

            $project = new Project();

            $project
                ->setName($name)
                ->setCreatedAt(strtotime($date))
                ->setSuspended(true)
            ;

            $manager->persist($project);
        }

        $groups = [
            'managers' => 'Company management',
            'staff'    => 'Company employees',
            'crew'     => 'Delivery guys',
        ];

        $members = [
            'managers' => [
                'hubert',
                'hermes',
            ],
            'staff' => [
                'fry',
                'bender',
                'leela',
                'amy',
                'zoidberg',
                'scruffy',
            ],
            'crew' => [
                'hubert',
                'fry',
                'bender',
                'leela',
                'amy',
            ],
        ];

        $project = new Project();

        $project
            ->setName('Planet Express')
            ->setDescription('Interplanetary delivery company')
            ->setCreatedAt(strtotime('1999-03-28'))
            ->setSuspended(false)
        ;

        foreach ($groups as $name => $description) {

            $group = new Group();

            $group
                ->setName(ucwords($name))
                ->setDescription($description)
                ->setProject($project)
            ;

            foreach ($members[$name] as $member) {
                /** @noinspection PhpParamsInspection */
                $group->addUser($this->getReference('user:' . $member));
            }

            $this->addReference('group:' . $name, $group);

            $manager->persist($group);
        }

        $this->addReference('project:planetexpress', $project);

        $manager->persist($project);
        $manager->flush();
    }
}
