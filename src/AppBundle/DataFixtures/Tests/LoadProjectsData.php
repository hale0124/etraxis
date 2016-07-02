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
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProjectsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    use ReflectionTrait;

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
        $this->loadEtraxisProjects($manager);
        $this->loadFuturamaProject($manager);
        $this->loadPhpFigProject($manager);
    }

    /**
     * Loads set of eTraxis projects.
     *
     * @param   ObjectManager $manager
     */
    protected function loadEtraxisProjects(ObjectManager $manager)
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
                ->setSuspended(true)
            ;

            $this->setProperty($project, 'createdAt', strtotime($date));

            $manager->persist($project);
        }

        $manager->flush();
    }

    /**
     * Loads Futurama project.
     *
     * @param   ObjectManager $manager
     */
    public function loadFuturamaProject(ObjectManager $manager)
    {
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
            ->setSuspended(false)
        ;

        $this->setProperty($project, 'createdAt', strtotime('1999-03-28'));

        foreach ($groups as $name => $description) {

            $group = new Group($project);

            $group
                ->setName(ucwords($name))
                ->setDescription($description)
            ;

            foreach ($members[$name] as $member) {
                /** @noinspection PhpParamsInspection */
                $group->addMember($this->getReference('user:' . $member));
            }

            $this->addReference('group:' . $name, $group);

            $manager->persist($group);
        }

        $this->addReference('project:planetexpress', $project);

        $manager->persist($project);
        $manager->flush();
    }

    /**
     * Loads "PHP-FIG" project.
     *
     * @param   ObjectManager $manager
     */
    public function loadPhpFigProject(ObjectManager $manager)
    {
        $groups = [
            'members' => 'Voting members',
        ];

        $members = [
            'members' => [
                'mwop',
                'pmjones',
                'seldaek',
                'mvriel',
                'Crell',
                'lsmith',
                'moufmouf',
                'korvinszanto',
                'manchuck',
            ],
        ];

        $project = new Project();

        $project
            ->setName('PHP-FIG')
            ->setDescription('PHP Framework Interop Group')
            ->setSuspended(false)
        ;

        $this->setProperty($project, 'createdAt', strtotime('2009-05-23'));

        foreach ($groups as $name => $description) {

            $group = new Group($project);

            $group
                ->setName(ucwords($name))
                ->setDescription($description)
            ;

            foreach ($members[$name] as $member) {
                /** @noinspection PhpParamsInspection */
                $group->addMember($this->getReference('user:' . $member));
            }

            $this->addReference('group:fig:' . $name, $group);

            $manager->persist($group);
        }

        $this->addReference('project:phpfig', $project);

        $manager->persist($project);
        $manager->flush();
    }
}
