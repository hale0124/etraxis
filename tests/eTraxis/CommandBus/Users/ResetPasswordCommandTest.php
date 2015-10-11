<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Tests\BaseTestCase;
use Rhumsaa\Uuid\Uuid;

class ResetPasswordCommandTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $username = 'artem';

        $user = $this->findUser($username);
        $this->assertNotNull($user);

        $command = new ForgotPasswordCommand([
            'username' => $username,
            'ip'       => '127.0.0.1',
        ]);

        $this->command_bus->handle($command);
    }

    public function testSuccess()
    {
        $expected = 'mzMEbtOdGC462vqQRa1nh9S7wyE='; // 'legacy'

        $user = $this->findUser('artem');

        $this->assertNotEquals($expected, $user->getPassword());

        $command = new ResetPasswordCommand([
            'token'    => $user->getResetToken(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser('artem');

        $this->assertEquals($expected, $user->getPassword());
        $this->assertNull($user->getResetToken());
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     * @expectedExceptionMessage This account uses an external authentication source. Impossible to change the password.
     */
    public function testLdap()
    {
        $username = 'einstein';
        $token    = Uuid::uuid4()->getHex();

        $user = $this->findUser($username, true);
        $this->assertNotNull($user);

        $user->setResetToken($token);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $user = $this->findUser($username, true);
        $this->assertNotNull($user);

        $command = new ResetPasswordCommand([
            'token'    => $user->getResetToken(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\CommandBus\CommandException
     * @expectedExceptionMessage Password should be at least 6 characters length.
     */
    public function testTooShort()
    {
        $user = $this->findUser('artem');

        $command = new ResetPasswordCommand([
            'token'    => $user->getResetToken(),
            'password' => 'short',
        ]);

        $this->command_bus->handle($command);
    }
}
