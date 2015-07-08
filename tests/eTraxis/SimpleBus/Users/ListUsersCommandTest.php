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

class ListUsersCommandTest extends BaseTestCase
{
    public function testBasic()
    {
        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $users = $this->doctrine->getRepository('eTraxis:User')->findAll();

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

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

        $command = new ListUsersCommand([
            'start'  => '',
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $this->assertEmpty($command->users);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $expected = 8;

        static::$kernel->getContainer()->set('security.authorization_checker', new AuthorizationCheckerAdminStub());

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => 'planetexpress',
            'order'  => [],
        ]);

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

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [
                ['column' => 5, 'dir' => 'desc'],
                ['column' => 2, 'dir' => 'asc'],
            ],
        ]);

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
