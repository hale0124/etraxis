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

use AltrEgo\AltrEgo;
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Tests\TransactionalTestCase;

class ResetPasswordCommandTest extends TransactionalTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $command = new ForgotPasswordCommand([
            'username' => 'artem',
            'ip'       => '127.0.0.1',
        ]);

        $this->command_bus->handle($command);
    }

    public function testSuccess()
    {
        $expected = 'mzMEbtOdGC462vqQRa1nh9S7wyE='; // 'legacy'

        $user = $this->findUser('artem');

        /** @var \StdClass $user2 */
        $user2 = AltrEgo::create($user);

        self::assertNotEquals($expected, $user->getPassword());

        $command = new ResetPasswordCommand([
            'token'    => $user2->resetToken,
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser('artem');

        self::assertEquals($expected, $user->getPassword());
        self::assertNull($user2->resetToken);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage This account uses an external authentication source. Impossible to change the password.
     */
    public function testLdap()
    {
        $username = 'einstein';

        $user = $this->findUser($username, AuthenticationProvider::LDAP);

        $token = $user->generateResetToken();

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $command = new ResetPasswordCommand([
            'token'    => $token,
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
        /** @var \StdClass $user */
        $user = AltrEgo::create($this->findUser('artem'));

        $command = new ResetPasswordCommand([
            'token'    => $user->resetToken,
            'password' => 'short',
        ]);

        $this->command_bus->handle($command);
    }
}
