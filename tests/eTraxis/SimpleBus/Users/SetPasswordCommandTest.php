<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
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