<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\States;

use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class AddStateResponsibleGroupsCommandTest extends TransactionalTestCase
{
    public function testAddResponsibleGroups()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var Group $crew */
        $crew = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Crew']);

        /** @var Group $managers */
        $managers = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        self::assertArraysByValues([$crew], $state->getResponsibleGroups());

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => $state->getId(),
            'groups' => [
                $crew->getId(),
                $managers->getId(),
            ],
        ]);

        $this->commandbus->handle($command);

        self::assertArraysByValues([$crew, $managers], $state->getResponsibleGroups());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Managers']);

        $command = new AddStateResponsibleGroupsCommand([
            'id'     => self::UNKNOWN_ENTITY_ID,
            'groups' => [$group->getId()],
        ]);

        $this->commandbus->handle($command);
    }
}
