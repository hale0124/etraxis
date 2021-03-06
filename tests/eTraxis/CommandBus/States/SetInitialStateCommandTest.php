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

use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use eTraxis\Tests\TransactionalTestCase;

class SetInitialStateCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var State $new */
        $new = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var State $delivered */
        $delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertEquals(StateType::IS_INITIAL, $new->getType());
        self::assertNotEquals(StateType::IS_INITIAL, $delivered->getType());

        $command = new SetInitialStateCommand(['id' => $delivered->getId()]);
        $this->commandbus->handle($command);

        $this->doctrine->getManager()->clear();

        $new       = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        $delivered = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        self::assertNotEquals(StateType::IS_INITIAL, $new->getType());
        self::assertEquals(StateType::IS_INITIAL, $delivered->getType());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFound()
    {
        $command = new SetInitialStateCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->commandbus->handle($command);
    }
}
