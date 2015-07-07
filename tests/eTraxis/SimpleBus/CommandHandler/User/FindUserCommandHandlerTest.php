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

namespace eTraxis\SimpleBus\CommandHandler\User;

use eTraxis\SimpleBus\Command\User\FindUserCommand;
use eTraxis\Tests\BaseTestCase;

class FindUserCommandHandlerTest extends BaseTestCase
{
    public function testFound()
    {
        /** @var \eTraxis\Model\User $user */
        $user = $this->doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => 'artem@eTraxis',
            'isLdap'   => false,
        ]);

        $command     = new FindUserCommand();
        $command->id = $user->getId();
        $this->command_bus->handle($command);

        $this->assertEquals($user->getId(), $command->user->getId());
    }

    public function testNotFound()
    {
        $command     = new FindUserCommand();
        $command->id = (1 << 31) - 1;
        $this->command_bus->handle($command);

        $this->assertNull($command->user);
    }
}
