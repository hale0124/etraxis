<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Entity\State;
use eTraxis\Entity\StateResponsibleGroup;
use eTraxis\Tests\BaseTestCase;

class AddStateResponsibleGroupsCommandTest extends BaseTestCase
{
    public function testAddResponsibleGroups()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        $responsible = $this->doctrine->getRepository(StateResponsibleGroup::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNull($responsible);

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $responsible = $this->doctrine->getRepository(StateResponsibleGroup::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNotNull($responsible);
    }

    public function testExistingResponsibleGroups()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertCount(1, $state->getResponsibleGroups());

        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [
                $crew->getId(),
                $managers->getId(),
            ],
        ]);

        $this->command_bus->handle($command);

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertCount(2, $state->getResponsibleGroups());
    }

    public function testEmptyResponsibleGroups()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'eTraxis 1.0']);

        $group = new Group();

        $group
            ->setProject($project)
            ->setName('Developers')
        ;

        $this->doctrine->getManager()->persist($group);
        $this->doctrine->getManager()->flush();

        $total = count($this->doctrine->getRepository(StateResponsibleGroup::class)->findAll());

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Produced']);
        self::assertNotNull($state);

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        self::assertCount($total, $this->doctrine->getRepository(StateResponsibleGroup::class)->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => self::UNKNOWN_ENTITY_ID,
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
