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
        $users = $this->doctrine->getRepository('eTraxis:User')->findAll();

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $this->assertEmpty($command->result['users']);

        $this->command_bus->handle($command);

        $this->assertNotEmpty($command->result['users']);

        $this->assertEquals(count($users), $command->result['total']);
        $this->assertEquals(count($users), count($command->result['users']));
    }

    /**
     * @expectedException     \eTraxis\SimpleBus\Middleware\ValidationException
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

        $this->assertEmpty($command->result['users']);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $expected = 8;

        $command = new ListUsersCommand([
            'start'  => 0,
            'length' => -1,
            'search' => 'planetexpress',
            'order'  => [],
        ]);

        $this->assertEmpty($command->result['users']);

        $this->command_bus->handle($command);

        $this->assertEquals($expected, $command->result['total']);
        $this->assertEquals($expected, count($command->result['users']));
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
            'einstein', // this one has NULL in the "description" field
            'artem',    // this one has NULL in the "description" field
        ];

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
        if ($this->client->getContainer()->getParameter('database_driver') == 'pdo_pgsql') {
            array_unshift($expected, array_pop($expected));
            array_unshift($expected, array_pop($expected));
        }

        $this->assertEmpty($command->result['users']);

        $this->command_bus->handle($command);

        $this->assertEquals(count($expected), count($command->result['users']));

        foreach ($expected as $index => $username) {
            $this->assertEquals($username, $command->result['users'][$index][1]);
        }
    }
}
