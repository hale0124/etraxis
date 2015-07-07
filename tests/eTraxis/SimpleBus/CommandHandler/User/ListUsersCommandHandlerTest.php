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

use eTraxis\SimpleBus\Command\User\ListUsersCommand;
use eTraxis\Tests\BaseTestCase;

class ListUsersCommandHandlerTest extends BaseTestCase
{
    public function testBasic()
    {
        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $users = $this->doctrine->getRepository('eTraxis:User')->findAll();

        $command = new ListUsersCommand();

        $command->start  = 0;
        $command->length = -1;
        $command->search = null;
        $command->order  = [];

        $this->assertEmpty($command->users);

        $this->command_bus->handle($command);

        $this->assertNotEmpty($command->users);
        $this->assertEquals(count($users), count($command->users));
    }

    /**
     * @expectedException     \eTraxis\Exception\ResponseException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $command = new ListUsersCommand();

        $command->start  = '';
        $command->length = -1;
        $command->search = null;
        $command->order  = [];

        $this->assertEmpty($command->users);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $expected = 8;

        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $command = new ListUsersCommand();

        $command->start  = 0;
        $command->length = -1;
        $command->search = 'planetexpress';
        $command->order  = [];

        $this->assertEmpty($command->users);

        $this->command_bus->handle($command);

        $this->assertEquals($expected, count($command->users));
    }

    public function testOrder()
    {
        $expected = [
            'zoidberg',
            'scruffy',
            'hermes',
            'hubert',
            'bender',
            'amy',
            'fry',
            'leela',
            'einstein', // this one has NULL in the "description" field
            'artem',    // this one has NULL in the "description" field
        ];

        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $command = new ListUsersCommand();

        $command->start  = 0;
        $command->length = -1;
        $command->search = null;
        $command->order  = [
            ['column' => 4, 'dir' => 'desc'],
            ['column' => 1, 'dir' => 'asc'],
        ];

        // PostgreSQL treats NULLs as greatest values.
        if (static::$kernel->getContainer()->getParameter('database_driver') == 'pdo_pgsql') {
            array_unshift($expected, array_pop($expected));
            array_unshift($expected, array_pop($expected));
        }

        $this->assertEmpty($command->users);

        $this->command_bus->handle($command);

        $this->assertEquals(count($expected), count($command->users));

        foreach ($expected as $index => $username) {
            $this->assertEquals($username, $command->users[$index][1]);
        }
    }
}
