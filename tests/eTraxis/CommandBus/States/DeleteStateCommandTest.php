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

use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Tests\TransactionalTestCase;

class DeleteStateCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State($template, StateType::IS_FINAL);

        $state
            ->setName('Cancelled')
            ->setAbbreviation('C')
            ->setResponsible(StateResponsible::REMOVE)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);
        self::assertNotNull($state);

        $command = new DeleteStateCommand(['id' => $state->getId()]);
        $this->commandbus->handle($command);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);
        self::assertNull($state);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        $command = new DeleteStateCommand(['id' => $state->getId()]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteStateCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->commandbus->handle($command);
    }
}
