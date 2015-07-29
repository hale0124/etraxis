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
use eTraxis\Entity\State;
use eTraxis\Entity\StateAssignee;
use eTraxis\Entity\StateRoleTransition;
use eTraxis\Model\SystemRole;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadStatesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        return 5;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $state_new       = new State();
        $state_delivered = new State();

        /** @noinspection PhpParamsInspection */
        $state_new
            ->setName('New')
            ->setAbbreviation('N')
            ->setType(State::TYPE_INITIAL)
            ->setResponsible(State::RESPONSIBLE_ASSIGN)
            ->setTemplate($this->getReference('template:delivery'))
            ->setNextState($state_delivered)
        ;

        /** @noinspection PhpParamsInspection */
        $state_delivered
            ->setName('Delivered')
            ->setAbbreviation('D')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
            ->setTemplate($this->getReference('template:delivery'))
        ;

        $this->addReference('state:new', $state_new);
        $this->addReference('state:delivered', $state_delivered);

        $manager->persist($state_new);
        $manager->persist($state_delivered);
        $manager->flush();

        $assignee = new StateAssignee();

        /** @noinspection PhpParamsInspection */
        $assignee
            ->setStateId($state_new->getId())
            ->setGroupId($this->getReference('group:crew')->getId())
            ->setState($state_new)
            ->setGroup($this->getReference('group:crew'))
        ;

        $transition = new StateRoleTransition();

        $transition
            ->setFromStateId($state_new->getId())
            ->setToStateId($state_delivered->getId())
            ->setRole(SystemRole::RESPONSIBLE)
            ->setFromState($state_new)
            ->setToState($state_delivered)
        ;

        $manager->persist($assignee);
        $manager->persist($transition);
        $manager->flush();
    }
}
