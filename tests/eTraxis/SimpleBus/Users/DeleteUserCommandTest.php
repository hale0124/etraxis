<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class DeleteUserCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('scruffy');
        self::assertNotNull($user);

        $command = new DeleteUserCommand(['id' => $user->getId()]);
        $this->command_bus->handle($command);

        $user = $this->findUser('scruffy');
        self::assertNull($user);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function testForbidden()
    {
        $this->loginAs('scruffy');

        $user = $this->findUser('scruffy');
        self::assertNotNull($user);

        $command = new DeleteUserCommand(['id' => $user->getId()]);
        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteUserCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
