<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Tests\BaseTestCase;

class SetPasswordCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        $expected = 'mzMEbtOdGC462vqQRa1nh9S7wyE='; // 'legacy'

        $user = $this->findUser('artem');

        $this->assertNotEquals($expected, $user->getPassword());

        $command = new SetPasswordCommand([
            'id'       => $user->getId(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);

        $user = $this->findUser('artem');

        $this->assertEquals($expected, $user->getPassword());
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     * @expectedExceptionMessage This account uses an external authentication source. Impossible to change the password.
     */
    public function testLdap()
    {
        $user = $this->findUser('einstein', true);
        $this->assertNotNull($user);

        $command = new SetPasswordCommand([
            'id'       => $user->getId(),
            'password' => 'legacy',
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * @expectedException \eTraxis\SimpleBus\CommandException
     * @expectedExceptionMessage Password should be at least 6 characters length.
     */
    public function testTooShort()
    {
        $user = $this->findUser('artem');

        $command = new SetPasswordCommand([
            'id'       => $user->getId(),
            'password' => 'short',
        ]);

        $this->command_bus->handle($command);
    }
}
