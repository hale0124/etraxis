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

use DataTables\DataTableQuery;
use DataTables\Parameters;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Component\HttpFoundation\Request;

class DataTableCachedResultsTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionTrait;

    /** @var DataTableQuery */
    protected $query;

    protected function setUp()
    {
        parent::setUp();

        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 20,
            'length'  => 10,
            'search'  => ['value' => 'symfony', 'regex' => true],
            'order'   => [
                ['column' => 1, 'dir' => 'desc'],
                ['column' => 0, 'dir' => 'asc'],
            ],
            'columns' => [
                ['data' => 0, 'name' => '#1', 'searchable' => true, 'orderable' => false, 'search' => ['value' => 'first', 'regex' => false]],
                ['data' => 1, 'name' => '#2', 'searchable' => false, 'orderable' => true, 'search' => ['value' => 'second', 'regex' => true]],
            ],
        ]);

        $parameters = new Parameters();

        $parameters->draw    = $request->get('draw');
        $parameters->start   = $request->get('start');
        $parameters->length  = $request->get('length');
        $parameters->search  = $request->get('search');
        $parameters->order   = $request->get('order');
        $parameters->columns = $request->get('columns');

        $this->query = new DataTableQuery($parameters);
    }

    public function testConstructor()
    {
        $expected = [
            'Monday',
            'Thursday',
            'Sunday',
        ];

        $results = new DataTableCachedResults($this->query, 7, $expected);

        self::assertEquals(7, $results->total);
        self::assertEquals($expected, $results->data);
    }

    public function testIsHit()
    {
        $results = new DataTableCachedResults($this->query);
        self::assertTrue($results->isHit($this->query));

        $this->setProperty($this->query, 'start', 10);
        self::assertTrue($results->isHit($this->query));

        $search = $this->query->search;
        $this->setProperty($search, 'value', 'laravel');
        $this->setProperty($this->query, 'search', $search);
        self::assertFalse($results->isHit($this->query));
    }
}
