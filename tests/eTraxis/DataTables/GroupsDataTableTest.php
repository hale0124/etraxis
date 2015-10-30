<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class GroupsDataTableTest extends BaseTestCase
{
    public function testBasic()
    {
        /** @var \eTraxis\Entity\Group[] $groups */
        $groups = $this->doctrine->getRepository('eTraxis:Group')->findAll();

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $this->assertNotEmpty($results['data']);

        $this->assertEquals(count($groups), $results['recordsTotal']);
        $this->assertEquals(count($groups), $results['recordsFiltered']);
        $this->assertEquals(count($groups), count($results['data']));
    }

    public function testSearch()
    {
        $total    = 5;
        $expected = 4;

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => 'plANeT', 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $this->assertEquals($total, $results['recordsTotal']);
        $this->assertEquals($expected, $results['recordsFiltered']);
        $this->assertEquals($expected, count($results['data']));
    }

    public function testFilterByName()
    {
        $expected = [
            'Crew',
            'Planet Express, Inc.',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => GroupsDataTable::COLUMN_NAME, 'search' => ['value' => 'rE', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByType()
    {
        $expected = [
            'Nimbus',
            'Planet Express, Inc.',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => GroupsDataTable::COLUMN_TYPE, 'search' => ['value' => 'global', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
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

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => GroupsDataTable::COLUMN_PROJECT, 'search' => ['value' => $project->getId(), 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'Crew',
            'Planet Express, Inc.',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => GroupsDataTable::COLUMN_DESCRIPTION, 'search' => ['value' => 'delivery', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'Managers',
            'Staff',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => GroupsDataTable::COLUMN_NAME,        'search' => ['value' => 'A',     'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => GroupsDataTable::COLUMN_TYPE,        'search' => ['value' => 'local', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => GroupsDataTable::COLUMN_DESCRIPTION, 'search' => ['value' => '',      'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
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

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => GroupsDataTable::COLUMN_PROJECT, 'dir' => 'desc'],
                ['column' => GroupsDataTable::COLUMN_NAME,    'dir' => 'asc'],
            ],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Group');

        $actual = [];

        foreach ($results['data'] as $group) {
            $actual[] = $group[GroupsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }
}
