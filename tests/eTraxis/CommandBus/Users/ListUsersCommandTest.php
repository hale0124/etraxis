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

class ListUsersCommandTest extends BaseTestCase
{
    public function testBasic()
    {
        $users = $this->doctrine->getRepository('eTraxis:User')->findAll();

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertNotEmpty($result['users']);

        $this->assertEquals(count($users), $result['total']);
        $this->assertEquals(count($users), $result['filtered']);
        $this->assertEquals(count($users), count($result['users']));
    }

    /**
     * @expectedException     \eTraxis\CommandBus\ValidationException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        $command = new ListUsersCommand([
            'start'  => '',
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $total    = 14;
        $expected = 8;

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => 'plANeTexprESS',
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertEquals($total, $result['total']);
        $this->assertEquals($expected, $result['filtered']);
        $this->assertEquals($expected, count($result['users']));
    }

    public function testFilterByUsername()
    {
        $expected = [
            'bender',
            'hermes',
            'hubert',
            'zoidberg',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'search' => ['value' => 'eR']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByFullname()
    {
        $expected = [
            'amy',
            'veins',
            'zoidberg',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_FULLNAME, 'search' => ['value' => 'dr.']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByEmail()
    {
        $expected = [
            'francine',
            'kif',
            'veins',
            'zapp',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_EMAIL, 'search' => ['value' => 'NimBUs']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByPermissionsAdmin()
    {
        $expected = [
            'artem',
            'hubert',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_PERMISSIONS, 'search' => ['value' => 'Admin']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByPermissionsUser()
    {
        $expected = 12;

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_PERMISSIONS, 'search' => ['value' => 'User']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertCount($expected, $result['users']);
    }

    public function testFilterByAuthenticationLdap()
    {
        $expected = [
            'einstein',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_AUTHENTICATION, 'search' => ['value' => 'LDAP']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByAuthenticationEtraxis()
    {
        $expected = 13;

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_AUTHENTICATION, 'search' => ['value' => 'eTraxis']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertCount($expected, $result['users']);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'scruffy',
            'veins',
            'zoidberg',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_DESCRIPTION, 'search' => ['value' => 'tOR']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'bender',
            'hermes',
            'hubert',
            'zoidberg',
        ];

        $command = new ListUsersCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'search' => ['value' => '']],
                ['data' => Handler\ListUsersCommandHandler::COLUMN_FULLNAME, 'search' => ['value' => 'eR']],
                ['data' => Handler\ListUsersCommandHandler::COLUMN_EMAIL,    'search' => ['value' => 'plANeTexprESS']],
            ],
            'order'   => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testOrder()
    {
        $expected = [
            'zoidberg',
            'francine',
            'scruffy',
            'hermes',
            'kif',
            'hubert',
            'veins',
            'bender',
            'amy',
            'fry',
            'leela',
            'zapp',
            'einstein',
            'artem',
        ];

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [
                ['column' => Handler\ListUsersCommandHandler::COLUMN_DESCRIPTION, 'dir' => 'desc'],
                ['column' => Handler\ListUsersCommandHandler::COLUMN_FULLNAME,    'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['users'] as $user) {
            $actual[] = $user[Handler\ListUsersCommandHandler::COLUMN_USERNAME];
        }

        $this->assertEquals($expected, $actual);
    }
}
