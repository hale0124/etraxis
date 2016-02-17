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

use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class DeleteStateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        $state = new State();

        $state
            ->setName('Cancelled')
            ->setAbbreviation('C')
            ->setType(State::TYPE_FINAL)
            ->setResponsible(State::RESPONSIBLE_REMOVE)
            ->setTemplate($template)
        ;

        $this->doctrine->getManager()->persist($state);
        $this->doctrine->getManager()->flush();

        $this->loginAs('hubert');

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);
        $this->assertNotNull($state);

        $command = new DeleteStateCommand(['id' => $state->getId()]);
        $this->command_bus->handle($command);

        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Cancelled']);
        $this->assertNull($state);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testAccessDenied()
    {
        $this->loginAs('hubert');

        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);
        $this->assertNotNull($state);

        $command = new DeleteStateCommand(['id' => $state->getId()]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown state.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteStateCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
