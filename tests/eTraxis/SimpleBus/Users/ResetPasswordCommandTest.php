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
use Ramsey\Uuid\Uuid;

class ResetPasswordCommandTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $username = 'artem';

        $user = $this->findUser($username);
        self::assertNotNull($user);

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

        self::assertNotEquals($expected, $user->getPassword());

        $command = new ResetPasswordCommand([
            'token'    => $user->getResetToken(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser('artem');

        self::assertEquals($expected, $user->getPassword());
        self::assertNull($user->getResetToken());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage This account uses an external authentication source. Impossible to change the password.
     */
    public function testLdap()
    {
        $username = 'einstein';
        $token    = Uuid::uuid4()->getHex();

        $user = $this->findUser($username, true);
        self::assertNotNull($user);

        $user->setResetToken($token);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $user = $this->findUser($username, true);
        self::assertNotNull($user);

        $command = new ResetPasswordCommand([
            'token'    => $user->getResetToken(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
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
