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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\State;
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateResponsibleGroup;
use eTraxis\Entity\StateRoleTransition;
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
        $this->loadDeliveryStates($manager);
        $this->loadFuturamaStates($manager);
    }

    /**
     * Loads states of "Delivery" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadDeliveryStates(ObjectManager $manager)
    {
        $state_new       = new State();
        $state_delivered = new State();

        /** @noinspection PhpParamsInspection */
        $state_new
            ->setTemplate($this->getReference('template:delivery'))
            ->setName('New')
            ->setAbbreviation('N')
            ->setType(State::TYPE_INITIAL)
            ->setResponsible(State::RESPONSIBLE_ASSIGN)
            ->setNextState($state_delivered)
        ;

        /** @noinspection PhpParamsInspection */
        $state_delivered
            ->setTemplate($this->getReference('template:delivery'))
            ->setName('Delivered')
            ->setAbbreviation('D')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
        ;

        $this->addReference('state:new', $state_new);
        $this->addReference('state:delivered', $state_delivered);

        $manager->persist($state_new);
        $manager->persist($state_delivered);
        $manager->flush();

        $responsible = new StateResponsibleGroup();

        /** @noinspection PhpParamsInspection */
        $responsible
            ->setState($state_new)
            ->setGroup($this->getReference('group:crew'))
        ;

        $group_transition = new StateGroupTransition();

        /** @noinspection PhpParamsInspection */
        $group_transition
            ->setFromState($state_new)
            ->setToState($state_delivered)
            ->setGroup($this->getReference('group:managers'))
        ;

        $role_transition = new StateRoleTransition();

        $role_transition
            ->setFromState($state_new)
            ->setToState($state_delivered)
            ->setRole(SystemRole::RESPONSIBLE)
        ;

        $manager->persist($responsible);
        $manager->persist($group_transition);
        $manager->persist($role_transition);
        $manager->flush();
    }

    /**
     * Loads states of "Futurama" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaStates(ObjectManager $manager)
    {
        $state_produced = new State();
        $state_released = new State();

        /** @noinspection PhpParamsInspection */
        $state_produced
            ->setTemplate($this->getReference('template:futurama'))
            ->setName('Produced')
            ->setAbbreviation('P')
            ->setType(State::TYPE_INITIAL)
            ->setResponsible(State::RESPONSIBLE_KEEP)
            ->setNextState($state_released)
        ;

        /** @noinspection PhpParamsInspection */
        $state_released
            ->setTemplate($this->getReference('template:futurama'))
            ->setName('Released')
            ->setAbbreviation('R')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
        ;

        $this->addReference('state:produced', $state_produced);
        $this->addReference('state:released', $state_released);

        $manager->persist($state_produced);
        $manager->persist($state_released);
        $manager->flush();

        $transition = new StateRoleTransition();

        $transition
            ->setFromState($state_produced)
            ->setToState($state_released)
            ->setRole(SystemRole::AUTHOR)
        ;

        $manager->persist($transition);
        $manager->flush();
    }
}
