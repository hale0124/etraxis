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
use eTraxis\Tests\BaseTestCase;

class AddRemoveStateAssigneesCommandTest extends BaseTestCase
{
    public function testAddAssignees()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\StateAssignee $assignee */
        $assignee = $this->doctrine->getRepository('eTraxis:StateAssignee')->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        $this->assertNull($assignee);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository('eTraxis:StateAssignee')->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        $this->assertNotNull($assignee);
    }

    public function testRemoveAssignees()
    {
        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $this->assertNotNull($state);

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Crew']);
        $this->assertNotNull($group);

        /** @var \eTraxis\Entity\StateAssignee $assignee */
        $assignee = $this->doctrine->getRepository('eTraxis:StateAssignee')->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        $this->assertNotNull($assignee);

        $command = new RemoveStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $assignee = $this->doctrine->getRepository('eTraxis:StateAssignee')->findOneBy([
            'stateId' => $state->getId(),
            'groupId' => $group->getId(),
        ]);
        $this->assertNull($assignee);
    }

    public function testEmptyAssignees()
    {
        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'eTraxis 1.0']);

        $group = new Group();

        $group
            ->setName('Developers')
            ->setProjectId($project->getId())
            ->setProject($project)
        ;

        $this->doctrine->getManager()->persist($group);
        $this->doctrine->getManager()->flush();

        $total = count($this->doctrine->getRepository('eTraxis:StateAssignee')->findAll());

        /** @var \eTraxis\Entity\State $state */
        $state = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Produced']);
        $this->assertNotNull($state);

        $command = new AddStateAssigneesCommand([
            'id'     => $state->getId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);

        $this->assertCount($total, $this->doctrine->getRepository('eTraxis:StateAssignee')->findAll());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var \eTraxis\Entity\Group $group */
        $group = $this->doctrine->getRepository('eTraxis:Group')->findOneBy(['name' => 'Managers']);
        $this->assertNotNull($group);

        $command = new AddStateAssigneesCommand([
            'id'     => $this->getMaxId(),
            'groups' => [$group->getId()],
        ]);

        $this->command_bus->handle($command);
    }
}
