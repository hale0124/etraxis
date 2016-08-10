<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use DataTables\DataTableQuery;
use DataTables\Parameters;
use eTraxis\DataTables\DataTableCachedResults;
use eTraxis\Service\RecordsCache\RecordsCacheService;
use eTraxis\Traits\ReflectionTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class RecordsCacheServiceTest extends KernelTestCase
{
    use ReflectionTrait;

    /** @var DataTableQuery */
    protected $query;

    /** @var RecordsCacheService */
    protected $cache;

    /** @var int */
    protected $user;

    protected function setUp()
    {
        self::bootKernel();

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
        $this->cache = new RecordsCacheService(static::$kernel->getContainer()->get('cache.app'));
        $this->user  = random_int(1, 100);
    }

    protected function tearDown()
    {
        $this->cache->deleteRecords($this->user);

        parent::tearDown();
    }

    public function testSave()
    {
        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertTrue(is_bool($records));
        self::assertFalse($records);

        $this->cache->saveRecords($this->user, new DataTableCachedResults($this->query));

        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertInstanceOf(DataTableCachedResults::class, $records);
    }

    public function testGetRecordsHit()
    {
        $this->cache->saveRecords($this->user, new DataTableCachedResults($this->query));

        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertInstanceOf(DataTableCachedResults::class, $records);
    }

    public function testGetRecordsNotHit()
    {
        $this->cache->saveRecords($this->user, new DataTableCachedResults($this->query));

        $search = $this->query->search;
        $this->setProperty($search, 'value', 'laravel');
        $this->setProperty($this->query, 'search', $search);

        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertTrue(is_bool($records));
        self::assertFalse($records);
    }

    public function testGetRecordsMissing()
    {
        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertTrue(is_bool($records));
        self::assertFalse($records);
    }

    public function testDelete()
    {
        $this->cache->saveRecords($this->user, new DataTableCachedResults($this->query));

        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertInstanceOf(DataTableCachedResults::class, $records);

        $this->cache->deleteRecords($this->user);

        $records = $this->cache->getRecords($this->user, $this->query);
        self::assertTrue(is_bool($records));
        self::assertFalse($records);
    }
}
