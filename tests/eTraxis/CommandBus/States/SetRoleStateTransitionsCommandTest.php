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

use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class SetRoleStateTransitionsCommandTest extends TransactionalTestCase
{
    /** @var State */
    private $draft;

    /** @var State */
    private $accepted;

    /** @var State */
    private $deprecated;

    protected function setUp()
    {
        parent::setUp();

        $repository = $this->doctrine->getRepository(State::class);

        $this->draft      = $repository->findOneBy(['name' => 'Draft']);
        $this->accepted   = $repository->findOneBy(['name' => 'Accepted']);
        $this->deprecated = $repository->findOneBy(['name' => 'Deprecated']);
    }

    public function testRoleTransitions()
    {
        self::assertEmpty($this->draft->getRoleTransitions(SystemRole::AUTHOR));

        $command = new SetRoleStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'role'        => SystemRole::AUTHOR,
            'transitions' => [
                $this->accepted->getId(),
            ],
        ]);

        $this->commandbus->handle($command);

        self::assertArraysByValues([$this->accepted], $this->draft->getRoleTransitions(SystemRole::AUTHOR));

        $command = new SetRoleStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'role'        => SystemRole::AUTHOR,
            'transitions' => [
                $this->accepted->getId(),
                $this->deprecated->getId(),
            ],
        ]);

        $this->commandbus->handle($command);

        self::assertArraysByValues([$this->accepted, $this->deprecated], $this->draft->getRoleTransitions(SystemRole::AUTHOR));

        $command = new SetRoleStateTransitionsCommand([
            'id'          => $this->draft->getId(),
            'role'        => SystemRole::AUTHOR,
            'transitions' => [
                $this->deprecated->getId(),
            ],
        ]);

        $this->commandbus->handle($command);

        self::assertArraysByValues([$this->deprecated], $this->draft->getRoleTransitions(SystemRole::AUTHOR));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFoundState()
    {
        $command = new SetRoleStateTransitionsCommand([
            'id'          => self::UNKNOWN_ENTITY_ID,
            'role'        => SystemRole::RESPONSIBLE,
            'transitions' => [$this->accepted->getId()],
        ]);

        $this->commandbus->handle($command);
    }
}
