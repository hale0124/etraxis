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
use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\State;
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
        $this->loadPhpPsrStates($manager);
    }

    /**
     * Loads states of "Delivery" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadDeliveryStates(ObjectManager $manager)
    {
        /** @var \eTraxis\Entity\Group $crew */
        $crew = $this->getReference('group:crew');

        /** @var \eTraxis\Entity\Template $template */
        $template = $this->getReference('template:delivery');

        $state_new       = new State($template, StateType::IS_INITIAL);
        $state_delivered = new State($template, StateType::IS_FINAL);

        $state_new
            ->setName('New')
            ->setAbbreviation('N')
            ->setResponsible(StateResponsible::ASSIGN)
            ->setNextState($state_delivered)
            ->addResponsibleGroups([$crew])
        ;

        $state_delivered
            ->setName('Delivered')
            ->setAbbreviation('D')
            ->setResponsible(StateResponsible::REMOVE)
        ;

        $this->addReference('state:new', $state_new);
        $this->addReference('state:delivered', $state_delivered);

        /** @var \eTraxis\Entity\Group $managers */
        $managers = $this->getReference('group:managers');

        $state_new->setRoleTransitions(SystemRole::RESPONSIBLE, [$state_delivered]);
        $state_new->setGroupTransitions($managers, [$state_delivered]);

        $manager->persist($state_new);
        $manager->persist($state_delivered);
        $manager->flush();
    }

    /**
     * Loads states of "Futurama" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaStates(ObjectManager $manager)
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->getReference('template:futurama');

        $state_produced = new State($template, StateType::IS_INITIAL);
        $state_released = new State($template, StateType::IS_FINAL);

        $state_produced
            ->setName('Produced')
            ->setAbbreviation('P')
            ->setResponsible(StateResponsible::KEEP)
            ->setNextState($state_released)
        ;

        $state_released
            ->setName('Released')
            ->setAbbreviation('R')
            ->setResponsible(StateResponsible::REMOVE)
        ;

        $this->addReference('state:produced', $state_produced);
        $this->addReference('state:released', $state_released);

        $state_produced->setRoleTransitions(SystemRole::AUTHOR, [$state_released]);

        $manager->persist($state_produced);
        $manager->persist($state_released);
        $manager->flush();
    }

    /**
     * Loads states of "PSR" template.
     *
     * @param   ObjectManager $manager
     */
    protected function loadPhpPsrStates(ObjectManager $manager)
    {
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->getReference('template:phppsr');

        $state_draft      = new State($template, StateType::IS_INITIAL);
        $state_accepted   = new State($template, StateType::IS_INTERIM);
        $state_deprecated = new State($template, StateType::IS_FINAL);

        $state_draft
            ->setName('Draft')
            ->setAbbreviation('D')
            ->setResponsible(StateResponsible::REMOVE)
            ->setNextState($state_accepted)
        ;

        $state_accepted
            ->setName('Accepted')
            ->setAbbreviation('A')
            ->setResponsible(StateResponsible::REMOVE)
        ;

        $state_deprecated
            ->setName('Deprecated')
            ->setAbbreviation('X')
            ->setResponsible(StateResponsible::REMOVE)
        ;

        $this->addReference('state:psr:draft', $state_draft);
        $this->addReference('state:psr:accepted', $state_accepted);
        $this->addReference('state:psr:deprecated', $state_deprecated);

        /** @var \eTraxis\Entity\Group $members */
        $members = $this->getReference('group:fig:members');

        $state_draft->setGroupTransitions($members, [$state_accepted]);
        $state_accepted->setGroupTransitions($members, [$state_deprecated]);

        $manager->persist($state_draft);
        $manager->persist($state_accepted);
        $manager->persist($state_deprecated);
        $manager->flush();
    }
}
