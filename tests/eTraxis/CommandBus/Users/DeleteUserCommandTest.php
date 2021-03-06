<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\TransactionalTestCase;

class DeleteUserCommandTest extends TransactionalTestCase
{
    public function testSuccess()
    {
        $this->loginAs('hubert');

        $user = $this->findUser('scruffy');
        self::assertNotNull($user);

        $command = new DeleteUserCommand(['id' => $user->getId()]);
        $this->commandbus->handle($command);

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

        $command = new DeleteUserCommand(['id' => $user->getId()]);
        $this->commandbus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @expectedExceptionMessage Unknown user.
     */
    public function testNotFound()
    {
        $this->loginAs('hubert');

        $command = new DeleteUserCommand(['id' => self::UNKNOWN_ENTITY_ID]);
        $this->commandbus->handle($command);
    }
}
