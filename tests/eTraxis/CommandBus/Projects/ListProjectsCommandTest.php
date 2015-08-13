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

namespace eTraxis\CommandBus\Projects;

use eTraxis\Tests\BaseTestCase;

class ListProjectsCommandTest extends BaseTestCase
{
    public function testBasic()
    {
        /** @var \eTraxis\Entity\Project[] $projects */
        $projects = $this->doctrine->getRepository('eTraxis:Project')->findAll();

        $command = new ListProjectsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertNotEmpty($result['projects']);

        $this->assertEquals(count($projects), $result['total']);
        $this->assertEquals(count($projects), $result['filtered']);
        $this->assertEquals(count($projects), count($result['projects']));
    }

    /**
     * @expectedException     \eTraxis\CommandBus\ValidationException
     * @expectedExceptionCode 400
     */
    public function testBadRequest()
    {
        $command = new ListProjectsCommand([
            'start'  => '',
            'length' => -1,
            'search' => null,
            'order'  => [],
        ]);

        $this->command_bus->handle($command);
    }

    public function testSearch()
    {
        $total    = 4;
        $expected = 3;

        $command = new ListProjectsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => 'Etraxis',
            'order'  => [],
        ]);

        $result = $this->command_bus->handle($command);

        $this->assertEquals($total, $result['total']);
        $this->assertEquals($expected, $result['filtered']);
        $this->assertEquals($expected, count($result['projects']));
    }

    public function testFilterByName()
    {
        $expected = [
            'eTraxis 1.0',
            'eTraxis 2.0',
            'eTraxis 3.0',
        ];

        $command = new ListProjectsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_NAME, 'search' => ['value' => 'Etraxis']],
            ],
            'order'   => [
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['projects'] as $project) {
            $actual[] = $project[Handler\ListProjectsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByStartTime()
    {
        $expected = [
            'eTraxis 1.0',
            'eTraxis 2.0',
        ];

        $command = new ListProjectsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_START_TIME, 'search' => ['value' => '12']],
            ],
            'order'   => [
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['projects'] as $project) {
            $actual[] = $project[Handler\ListProjectsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'Planet Express',
        ];

        $command = new ListProjectsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_DESCRIPTION, 'search' => ['value' => 'delivery']],
            ],
            'order'   => [
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['projects'] as $project) {
            $actual[] = $project[Handler\ListProjectsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'eTraxis 2.0',
        ];

        $command = new ListProjectsCommand([
            'start'   => 0,
            'length'  => -1,
            'search'  => null,
            'columns' => [
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_NAME,        'search' => ['value' => 'Etraxis']],
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_START_TIME,  'search' => ['value' => '9-']],
                ['data' => Handler\ListProjectsCommandHandler::COLUMN_DESCRIPTION, 'search' => ['value' => '']],
            ],
            'order'   => [
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_NAME, 'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['projects'] as $project) {
            $actual[] = $project[Handler\ListProjectsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testOrder()
    {
        $expected = [
            'Planet Express',
            'eTraxis 1.0',
            'eTraxis 2.0',
            'eTraxis 3.0',
        ];

        $command = new ListProjectsCommand([
            'start'  => 0,
            'length' => -1,
            'search' => null,
            'order'  => [
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_DESCRIPTION, 'dir' => 'desc'],
                ['column' => Handler\ListProjectsCommandHandler::COLUMN_NAME,        'dir' => 'asc'],
            ],
        ]);

        $result = $this->command_bus->handle($command);

        $actual = [];

        foreach ($result['projects'] as $project) {
            $actual[] = $project[Handler\ListProjectsCommandHandler::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }
}
