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
use eTraxis\Entity\StateAssignee;
use eTraxis\Tests\BaseTestCase;

class AddRemoveStateAssigneesCommandTest extends BaseTestCase
{
    public function testAddAssignees()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);
        self::assertNotNull($group);

        /** @var StateAssignee $assignee */
        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        self::assertNull($assignee);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        self::assertNotNull($assignee);
    }

    public function testRemoveAssignees()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertNotNull($state);

        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);
        self::assertNotNull($group);

        /** @var StateAssignee $assignee */
        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        self::assertNotNull($assignee);

        $command = new RemoveStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        self::assertNull($assignee);
    }

    public function testEmptyAssignees()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'eTraxis 1.0']);

        $group = new Group();

        $group
            ->setName('Developers')
            ->setProjectId($project->getId())
            ->setProject($project)
        ;

        $this->doctrine->getManager()->persist($group);
        $this->doctrine->getManager()->flush();

        $total = count($this->doctrine->getRepository(StateAssignee::class)->findAll());

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Produced']);
        self::assertNotNull($state);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        self::assertCount($total, $this->doctrine->getRepository(StateAssignee::class)->findAll());
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

        $command = new AddStateAssigneesCommand([
            'id'     => $this->getMaxId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
