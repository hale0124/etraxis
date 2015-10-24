<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables\ORM;

use eTraxis\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;

class ProjectsDataTableTest extends BaseTestCase
{
    public function testBasic()
    {
        /** @var \eTraxis\Entity\Project[] $projects */
        $projects = $this->doctrine->getRepository('eTraxis:Project')->findAll();

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $this->assertEquals(count($projects), $results['recordsTotal']);
        $this->assertEquals(count($projects), $results['recordsFiltered']);
        $this->assertEquals(count($projects), count($results['data']));
    }

    public function testSearch()
    {
        $total    = 4;
        $expected = 3;

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => 'Etraxis', 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $this->assertEquals($total, $results['recordsTotal']);
        $this->assertEquals($expected, $results['recordsFiltered']);
        $this->assertEquals($expected, count($results['data']));
    }

    public function testFilterByName()
    {
        $expected = [
            'eTraxis 1.0',
            'eTraxis 2.0',
            'eTraxis 3.0',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => ProjectsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => ProjectsDataTable::COLUMN_NAME, 'search' => ['value' => 'Etraxis', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $actual = [];

        foreach ($results['data'] as $project) {
            $actual[] = $project[ProjectsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByStartTime()
    {
        $expected = [
            'eTraxis 1.0',
            'eTraxis 2.0',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => ProjectsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => ProjectsDataTable::COLUMN_START_TIME, 'search' => ['value' => '12', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $actual = [];

        foreach ($results['data'] as $project) {
            $actual[] = $project[ProjectsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'Planet Express',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => ProjectsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => ProjectsDataTable::COLUMN_DESCRIPTION, 'search' => ['value' => 'delivery', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $actual = [];

        foreach ($results['data'] as $project) {
            $actual[] = $project[ProjectsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'eTraxis 2.0',
        ];

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => ProjectsDataTable::COLUMN_NAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => ProjectsDataTable::COLUMN_NAME,        'search' => ['value' => 'Etraxis', 'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => ProjectsDataTable::COLUMN_START_TIME,  'search' => ['value' => '9-',      'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => ProjectsDataTable::COLUMN_DESCRIPTION, 'search' => ['value' => '',        'regex' => 'false'], 'name' => null, 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $actual = [];

        foreach ($results['data'] as $project) {
            $actual[] = $project[ProjectsDataTable::COLUMN_NAME];
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

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => ProjectsDataTable::COLUMN_DESCRIPTION, 'dir' => 'desc'],
                ['column' => ProjectsDataTable::COLUMN_NAME,        'dir' => 'asc'],
            ],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Project');

        $actual = [];

        foreach ($results['data'] as $project) {
            $actual[] = $project[ProjectsDataTable::COLUMN_NAME];
        }

        $this->assertEquals($expected, $actual);
    }
}
