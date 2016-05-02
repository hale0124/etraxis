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

class AddStateAssigneesCommandTest extends BaseTestCase
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
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNull($assignee);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository(StateAssignee::class)->findOneBy([
            'state' => $state,
            'group' => $group,
        ]);
        self::assertNotNull($assignee);
    }

    public function testExistingAssignees()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertCount(1, $state->getAssigneeGroups());

        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [
                $crew->getId(),
                $managers->getId(),
            ],
        ]);

        $this->command_bus->handle($command);

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        self::assertCount(2, $state->getAssigneeGroups());
    }

    public function testEmptyAssignees()
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
            'id'     => PHP_INT_MAX,
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
