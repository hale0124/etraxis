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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadGroupsData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = [

            'Planet Express, Inc.' => [
                'description' => 'Intergalactic delivery company',
                'members'     => [
                    'amy',
                    'bender',
                    'fry',
                    'hermes',
                    'hubert',
                    'leela',
                    'scruffy',
                    'zoidberg',
                ],
            ],

            'Nimbus' => [
                'description' => 'Flagship of the DOOP fleet',
                'members'     => [
                    'francine',
                    'kif',
                    'veins',
                    'zapp',
                ],
            ],
        ];

        foreach ($data as $name => $row) {

            $group = new Group();

            $group
                ->setName($name)
                ->setDescription($row['description'])
            ;

            foreach ($row['members'] as $member) {
                /** @noinspection PhpParamsInspection */
                $group->addMember($this->getReference('user:' . $member));
            }

            $manager->persist($group);
        }

        $manager->flush();
    }
}
