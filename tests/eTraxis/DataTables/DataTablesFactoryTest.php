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

class SuccessfulTestDataTable implements DataTableInterface
{
    public function handle(DataTableQuery $query)
    {
        $results = new DataTableResults();

        $results->recordsTotal    = 100;
        $results->recordsFiltered = 10;
        $results->data            = [];

        return $results;
    }
}

class ExceptionTestDataTable implements DataTableInterface
{
    public function handle(DataTableQuery $query)
    {
        throw new DataTableException('Something gone wrong.');
    }
}

class NoInterfaceTestDataTable
{
    public function handle()
    {
        $results = new DataTableResults();

        $results->recordsTotal    = 100;
        $results->recordsFiltered = 10;
        $results->data            = [];

        return $results;
    }
}

class InvalidResultsTestDataTable implements DataTableInterface
{
    public function handle(DataTableQuery $query)
    {
        $results = new DataTableResults();

        $results->recordsTotal    = 100;
        $results->recordsFiltered = 10;
        $results->data            = null;

        return $results;
    }
}

class DataTablesFactoryTest extends BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->getContainer()->set('datatable.test.success',   new SuccessfulTestDataTable());
        $this->client->getContainer()->set('datatable.test.exception', new ExceptionTestDataTable());
        $this->client->getContainer()->set('datatable.test.interface', new NoInterfaceTestDataTable());
        $this->client->getContainer()->set('datatable.test.invalid',   new InvalidResultsTestDataTable());

        /** @var \eTraxis\DataTables\DataTablesFactory $datatables */
        $datatables = $this->datatables;
        $datatables->addService('datatable.test.success',   'eTraxis:TestSuccess');
        $datatables->addService('datatable.test.exception', 'eTraxis:TestException');
        $datatables->addService('datatable.test.interface', 'eTraxis:TestInterface');
        $datatables->addService('datatable.test.invalid',   'eTraxis:TestInvalid');
    }

    public function testSuccess()
    {
        $draw = mt_rand();

        $request = new Request([
            'draw'    => $draw,
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $expected = [
            'draw'            => $draw,
            'recordsTotal'    => 100,
            'recordsFiltered' => 10,
            'data'            => [],
        ];

        $results = $this->datatables->handle($request, 'eTraxis:TestSuccess');

        $this->assertEquals($expected, $results);
    }

    /**
     * @expectedException \eTraxis\DataTables\DataTableException
     * @expectedExceptionMessage Something gone wrong.
     */
    public function testException()
    {
        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $this->datatables->handle($request, 'eTraxis:TestException');
    }

    /**
     * @expectedException \eTraxis\DataTables\DataTableException
     * @expectedExceptionMessage This value should not be null.
     */
    public function testBadQuery()
    {
        $request = new Request([
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $this->datatables->handle($request, 'eTraxis:TestSuccess');
    }

    /**
     * @expectedException \eTraxis\DataTables\DataTableException
     * @expectedExceptionMessage Unknown entity to process with DataTable service.
     */
    public function testUnknownService()
    {
        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $this->datatables->handle($request, 'eTraxis:TestUnknown');
    }

    /**
     * @expectedException \eTraxis\DataTables\DataTableException
     * @expectedExceptionMessage DataTable service must implement "DataTableInterface" interface.
     */
    public function testNoInterface()
    {
        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $this->datatables->handle($request, 'eTraxis:TestInterface');
    }

    /**
     * @expectedException \eTraxis\DataTables\DataTableException
     * @expectedExceptionMessage This value should not be null.
     */
    public function testInvalidResults()
    {
        $request = new Request([
            'draw'    => mt_rand(),
            'start'   => 0,
            'length'  => 10,
            'search'  => ['value' => null, 'regex' => 'false'],
            'order'   => [],
            'columns' => [],
        ]);

        $this->datatables->handle($request, 'eTraxis:TestInvalid');
    }
}
