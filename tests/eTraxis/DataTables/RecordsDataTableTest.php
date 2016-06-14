<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\DataTables;

use eTraxis\Tests\TransactionalTestCase;
use Symfony\Component\HttpFoundation\Request;

class RecordsDataTableTest extends TransactionalTestCase
{
    /** @var \DataTables\DataTablesInterface */
    protected $datatables;

    protected function setUp()
    {
        parent::setUp();

        $this->datatables = $this->client->getContainer()->get('datatables');
    }

    public function testBasic()
    {
        $expected_by_user = [
            'artem'  => 128,    // all "Futurama" records.
            'hubert' => 149,    // 128 "Futurama" + 21 "Delivery" records
            'mwop'   => 143,    // 128 "Futurama" + 15 "PSR" records
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        foreach ($expected_by_user as $user => $expected) {

            $this->loginAs($user);
            $results = $this->datatables->handle($request, 'eTraxis:Record');

            self::assertNotEmpty($results['data']);

            self::assertEquals($expected, $results['recordsTotal']);
            self::assertEquals($expected, $results['recordsFiltered']);
            self::assertCount($expected, $results['data']);
        }
    }

    public function testSearch()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 3;

        $expected = [
            ['Planet Express', 'R', 'Artem Rodygin',     '&mdash;', 'I Second That Emotion'],
            ['PHP-FIG',        'D', 'Lukas Kahwe Smith', '&mdash;', 'Security Advisories'],
            ['PHP-FIG',        'D', 'Lukas Kahwe Smith', '&mdash;', 'Security Reporting Process'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => 'sEc', 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testFilterByRecordId()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 15;

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_RECORD_ID, 'search' => ['value' => 'FiG', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
    }

    public function testFilterByProject()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 128;

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_PROJECT, 'search' => ['value' => 'eXPreSs', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
    }

    public function testFilterByState()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 6;

        $expected = [
            ['PHP-FIG', 'A', 'Paul M. Jones',            '&mdash;', 'Basic Coding Standard'],
            ['PHP-FIG', 'A', 'Paul M. Jones',            '&mdash;', 'Coding Style Guide'],
            ['PHP-FIG', 'A', 'Jordi Boggiano',           '&mdash;', 'Logger Interface'],
            ['PHP-FIG', 'A', 'Paul M. Jones',            '&mdash;', 'Autoloading Standard'],
            ['PHP-FIG', 'A', 'Larry Garfield',           '&mdash;', 'Caching Interface'],
            ['PHP-FIG', 'A', 'Matthew Weier O\'Phinney', '&mdash;', 'HTTP Message Interface'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_STATE, 'search' => ['value' => 'a', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testFilterBySubject()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 3;

        $expected = [
            ['Planet Express', 'R', 'Artem Rodygin',     '&mdash;', 'I Second That Emotion'],
            ['PHP-FIG',        'D', 'Lukas Kahwe Smith', '&mdash;', 'Security Advisories'],
            ['PHP-FIG',        'D', 'Lukas Kahwe Smith', '&mdash;', 'Security Reporting Process'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_SUBJECT, 'search' => ['value' => 'sec', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testFilterByAuthor()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 3;

        $expected = [
            ['PHP-FIG', 'A', 'Larry Garfield', '&mdash;', 'Caching Interface'],
            ['PHP-FIG', 'D', 'Larry Garfield', '&mdash;', 'Huggable Interface'],
            ['PHP-FIG', 'D', 'Larry Garfield', '&mdash;', 'Hypermedia Links'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_AUTHOR, 'search' => ['value' => 'gARfiEld', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testFilterByResponsible()
    {
        $this->loginAs('hubert');

        $total    = 149;
        $filtered = 3;

        $expected = [
            ['Planet Express', 'N', 'Hubert J. Farnsworth', 'Turanga Leela', 'e-Waste'],
            ['Planet Express', 'N', 'Hubert J. Farnsworth', 'Turanga Leela', 'New clamps for Francis X. Clampazzo.'],
            ['Planet Express', 'N', 'Hubert J. Farnsworth', 'Turanga Leela', 'A statue commemorating the loss of the first Planet Express crew'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_RESPONSIBLE, 'search' => ['value' => 'leELa', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testFilterByAge()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 1;

        $expected = [
            ['PHP-FIG', 'X', 'Matthew Weier O\'Phinney', '&mdash;', 'Autoloading Standard'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_AGE, 'search' => ['value' => '1433', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $this->loginAs('mwop');

        $total    = 143;
        $filtered = 2;

        $expected = [
            ['PHP-FIG', 'A', 'Larry Garfield', '&mdash;', 'Caching Interface'],
            ['PHP-FIG', 'D', 'Larry Garfield', '&mdash;', 'Huggable Interface'],
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_SUBJECT, 'search' => ['value' => 'intERfACe', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => RecordsDataTable::COLUMN_AUTHOR,  'search' => ['value' => 'lArRY',     'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = [
                $user[RecordsDataTable::COLUMN_PROJECT],
                $user[RecordsDataTable::COLUMN_STATE],
                $user[RecordsDataTable::COLUMN_AUTHOR],
                $user[RecordsDataTable::COLUMN_RESPONSIBLE],
                $user[RecordsDataTable::COLUMN_SUBJECT],
            ];
        }

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($filtered, $results['recordsFiltered']);
        self::assertEquals($expected, $actual);
    }

    public function testOrder()
    {
        $this->loginAs('mwop');

        $expected = [
            // State = 'A'
            'Basic Coding Standard'      => 'Paul M. Jones',
            'Coding Style Guide'         => 'Paul M. Jones',
            'Autoloading Standard'       => 'Paul M. Jones',
            'HTTP Message Interface'     => 'Matthew Weier O\'Phinney',
            'Caching Interface'          => 'Larry Garfield',
            'Logger Interface'           => 'Jordi Boggiano',
            // State = 'D'
            'PHPDoc Standard'            => 'Mike van Riel',
            'Security Advisories'        => 'Lukas Kahwe Smith',
            'Security Reporting Process' => 'Lukas Kahwe Smith',
            'Huggable Interface'         => 'Larry Garfield',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => RecordsDataTable::COLUMN_STATE,  'dir' => 'asc'],
                ['column' => RecordsDataTable::COLUMN_AUTHOR, 'dir' => 'desc'],
            ],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $record) {
            $subject = $record[RecordsDataTable::COLUMN_SUBJECT];
            $author  = $record[RecordsDataTable::COLUMN_AUTHOR];

            $actual[$subject] = $author;
        }

        self::assertEquals($expected, $actual);
    }

    public function testPagination()
    {
        $this->loginAs('mwop');

        // 2nd (last) page
        $expected = [
            // State = 'D'
            'Hypermedia Links'            => 'Larry Garfield',
            'Extended Coding Style Guide' => 'Korvin Szanto',
            'Container Interface'         => 'David Négrier',
            'Event Manager'               => 'Chuck Reeves',
            // State = 'X'
            'Autoloading Standard'        => 'Matthew Weier O\'Phinney',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 10,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => RecordsDataTable::COLUMN_STATE,  'dir' => 'asc'],
                ['column' => RecordsDataTable::COLUMN_AUTHOR, 'dir' => 'desc'],
            ],
            'columns' => [
                ['data' => RecordsDataTable::COLUMN_RECORD_ID, 'search' => ['value' => 'fig', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:Record');

        $actual = [];

        foreach ($results['data'] as $record) {
            $subject = $record[RecordsDataTable::COLUMN_SUBJECT];
            $author  = $record[RecordsDataTable::COLUMN_AUTHOR];

            $actual[$subject] = $author;
        }

        self::assertEquals($expected, $actual);
        self::assertEquals(143, $results['recordsTotal']);
        self::assertEquals(15, $results['recordsFiltered']);
    }
}
