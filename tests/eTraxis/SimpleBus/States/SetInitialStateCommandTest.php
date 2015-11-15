<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class SetInitialStateCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\State $new */
        $new = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);

        /** @var \eTraxis\Entity\State $delivered */
        $delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $this->assertEquals(State::TYPE_INITIAL, $new->getType());
        $this->assertNotEquals(State::TYPE_INITIAL, $delivered->getType());

        $command = new SetInitialStateCommand(['id' => $delivered->getId()]);
        $this->command_bus->handle($command);

        $this->doctrine->getManager()->clear();

        $new       = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'New']);
        $delivered = $this->doctrine->getRepository('eTraxis:State')->findOneBy(['name' => 'Delivered']);

        $this->assertNotEquals(State::TYPE_INITIAL, $new->getType());
        $this->assertEquals(State::TYPE_INITIAL, $delivered->getType());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFound()
    {
        $command = new SetInitialStateCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
