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

use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;
use Symfony\Component\HttpFoundation\Request;

class UsersDataTableTest extends TransactionalTestCase
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
        $users = $this->doctrine->getRepository(User::class)->findAll();

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        self::assertNotEmpty($results['data']);

        self::assertCount($results['recordsTotal'], $users);
        self::assertCount($results['recordsFiltered'], $users);
        self::assertEquals(count($users), count($results['data']));
    }

    public function testSearch()
    {
        $total    = 23;
        $expected = 8;

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => 'plANeTexprESS', 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        self::assertEquals($total, $results['recordsTotal']);
        self::assertEquals($expected, $results['recordsFiltered']);
        self::assertCount($expected, $results['data']);
    }

    public function testFilterByUsername()
    {
        $expected = [
            'bender',
            'hermes',
            'hubert',
            'zoidberg',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_USERNAME, 'search' => ['value' => 'eR', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testFilterByFullname()
    {
        $expected = [
            'amy',
            'veins',
            'zoidberg',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_FULLNAME, 'search' => ['value' => 'dr.', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testFilterByEmail()
    {
        $expected = [
            'francine',
            'kif',
            'veins',
            'zapp',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_EMAIL, 'search' => ['value' => 'NimBUs', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testFilterByPermissionsAdmin()
    {
        $expected = [
            'artem',
            'hubert',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_PERMISSIONS, 'search' => ['value' => 'Admin', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testFilterByPermissionsUser()
    {
        $expected = 21;

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_PERMISSIONS, 'search' => ['value' => 'User', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        self::assertCount($expected, $results['data']);
    }

    public function testFilterByAuthenticationLdap()
    {
        $expected = [
            'einstein',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_AUTHENTICATION, 'search' => ['value' => 'LDAP', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testFilterByAuthenticationEtraxis()
    {
        $expected = 22;

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_AUTHENTICATION, 'search' => ['value' => 'eTraxis', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        self::assertCount($expected, $results['data']);
    }

    public function testFilterByDescription()
    {
        $expected = [
            'mvriel',
            'scruffy',
            'veins',
            'zoidberg',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_DESCRIPTION, 'search' => ['value' => 'tOR', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testCombinedFilter()
    {
        $expected = [
            'bender',
            'hermes',
            'hubert',
            'zoidberg',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => -1,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_USERNAME, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => UsersDataTable::COLUMN_USERNAME, 'search' => ['value' => '',              'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => UsersDataTable::COLUMN_FULLNAME, 'search' => ['value' => 'eR',            'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
                ['data' => UsersDataTable::COLUMN_EMAIL,    'search' => ['value' => 'plANeTexprESS', 'regex' => 'false'], 'name' => '', 'searchable' => 'true', 'orderable' => 'true'],
            ],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_USERNAME];
        }

        self::assertEquals($expected, $actual);
    }

    public function testOrder()
    {
        $expected = [
            'David Négrier'             => null,
            'Chuck Reeves'              => null,
            'Artem Rodygin'             => null,
            'Albert Einstein'           => null,
            'Paul M. Jones'             => 'Aura Project and Solar Framework',
            'Zapp Brannigan'            => 'Captain',
            'Jordi Boggiano'            => 'Composer',
            'Korvin Szanto'             => 'concrete5',
            'Turanga Leela'             => 'Delivery Crew',
            'Philip J. Fry'             => 'Delivery Crew',
            'Dr. Amy Wong'              => 'Delivery Crew',
            'Bender Bending Rodriguez'  => 'Delivery Crew',
            'Dr. Veins McGee'           => 'Doctor',
            'Larry Garfield'            => 'Drupal',
            'Hubert J. Farnsworth'      => 'Founder / Owner',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 0,
            'length'  => 15,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_DESCRIPTION, 'dir' => 'asc'],
                ['column' => UsersDataTable::COLUMN_FULLNAME,    'dir' => 'desc'],
            ],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $fullname    = $user[UsersDataTable::COLUMN_FULLNAME];
            $description = $user[UsersDataTable::COLUMN_DESCRIPTION];

            $actual[$fullname] = $description;
        }

        self::assertEquals($expected, $actual);
    }

    public function testPagination()
    {
        // 3rd (last) page
        $expected = [
            'Artem Rodygin',
            'Chuck Reeves',
            'David Négrier',
        ];

        $request = new Request([
            'draw'    => random_int(1, PHP_INT_MAX),
            'start'   => 20,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [
                ['column' => UsersDataTable::COLUMN_DESCRIPTION, 'dir' => 'desc'],
                ['column' => UsersDataTable::COLUMN_FULLNAME,    'dir' => 'asc'],
            ],
            'columns' => [],
        ]);

        $results = $this->datatables->handle($request, 'eTraxis:User');

        $actual = [];

        foreach ($results['data'] as $user) {
            $actual[] = $user[UsersDataTable::COLUMN_FULLNAME];
        }

        self::assertEquals($expected, $actual);
        self::assertEquals(23, $results['recordsTotal']);
    }
}
