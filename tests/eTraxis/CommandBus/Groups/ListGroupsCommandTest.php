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

namespace eTraxis\CommandBus\Groups;

use eTraxis\Tests\BaseTestCase;

class ListGroupsCommandTest extends BaseTestCase
{
    public function testBasic()
    {
        /** @var \eTraxis\Entity\Group[] $groups */
        $groups = $this->doctrine->getRepository('eTraxis:Group')->findAll();

        $command = new ListGroupsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertNotEmpty($result['groups']);

        $this->assertEquals(count($groups), $result['total']);
        $this->assertEquals(count($groups), $result['filtered']);
        $this->assertEquals(count($groups), count($result['groups']));
    }

    /**
     * @expectedException     \eTraxis\CommandBus\ValidationException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        $command = new ListGroupsCommand([
            'start'  => '',
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $total    = 5;
        $expected = 4;

        $command = new ListGroupsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => 'plANeT',
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertEquals($total, $result['total']);
        $this->assertEquals($expected, $result['filtered']);
        $this->assertEquals($expected, count($result['groups']));
    }

    public function testFilterByName()
    {
        $expected = [
            'Crew',
            'Planet Express, Inc.',
        ];

        $command = new ListGroupsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'search' => ['value' => 'rE']],
            ],
            'order'   => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByType()
    {
        $expected = [
            'Nimbus',
            'Planet Express, Inc.',
        ];

        $command = new ListGroupsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_TYPE, 'search' => ['value' => 'global']],
            ],
            'order'   => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByProject()
    {
        $expected = [
            'Crew',
            'Managers',
            'Staff',
        ];

        /** @var \eTraxis\Entity\Project $project */
        $project = $this->doctrine->getRepository('eTraxis:Project')->findOneBy(['name' => 'Planet Express']);

        $command = new ListGroupsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_PROJECT, 'search' => ['value' => $project->getId()]],
            ],
            'order'   => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'Crew',
            'Planet Express, Inc.',
        ];

        $command = new ListGroupsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_DESCRIPTION, 'search' => ['value' => 'delivery']],
            ],
            'order'   => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'Managers',
            'Staff',
        ];

        $command = new ListGroupsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_NAME,        'search' => ['value' => 'A']],
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_TYPE,        'search' => ['value' => 'local']],
                ['data' => Handler\ListGroupsCommandHandler::COLUMN_DESCRIPTION, 'search' => ['value' => '']],
            ],
            'order'   => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testOrder()
    {
        $expected = [
            'Crew',
            'Managers',
            'Staff',
            'Nimbus',
            'Planet Express, Inc.',
        ];

        $command = new ListGroupsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_PROJECT, 'dir' => 'desc'],
                ['column' => Handler\ListGroupsCommandHandler::COLUMN_NAME,    'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['groups'] as $group) {
            $actual[] = $group[Handler\ListGroupsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }
}
