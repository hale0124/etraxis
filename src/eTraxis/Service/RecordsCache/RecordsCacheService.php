<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\RecordsCache;

use DataTables\DataTableQuery;
use DataTables\DataTableResults;
use eTraxis\Constant\Seconds;
use eTraxis\DataTables\DataTableCachedResults;
use eTraxis\DataTables\RecordsDataTable;
use eTraxis\Service\RecordsCacheInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Records cache service.
 */
class RecordsCacheService implements RecordsCacheInterface
{
    protected $cache;

    /**
     * Dependency Injection constructor.
     *
     * @param   CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRecords(int $user, DataTableCachedResults $records)
    {
        $key  = $this->getKey($user);
        $item = $this->cache->getItem($key);

        $item->set($records);
        $item->expiresAfter(Seconds::FIVE_MINUTES);

        $this->cache->save($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getRecords(int $user, DataTableQuery $request)
    {
        $key  = $this->getKey($user);
        $item = $this->cache->getItem($key);

        if (!$item->isHit()) {
            return false;
        }

        /** @var DataTableCachedResults $records */
        $records = $item->get();

        if (!$records->isHit($request)) {
            return false;
        }

        return $records;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRecords(int $user)
    {
        $this->cache->deleteItem($this->getKey($user));
    }

    /**
     * {@inheritdoc}
     */
    public function markRecordsAsRead(int $user, array $ids)
    {
        $key  = $this->getKey($user);
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {

            /** @var DataTableCachedResults $records */
            $records = $item->get();

            array_walk($records->data, function (array &$record) use ($ids) {

                if (in_array($record[RecordsDataTable::COLUMN_ID] ?? null, $ids)) {
                    $row_class = $record[DataTableResults::DT_ROW_CLASS];
                    $row_class = str_replace('unread', null, $row_class);
                    $row_class = preg_replace('/\s+/', ' ', $row_class);

                    $record[DataTableResults::DT_ROW_CLASS] = trim($row_class);
                }
            });

            $item->set($records);
            $item->expiresAfter(Seconds::FIVE_MINUTES);

            $this->cache->save($item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markRecordsAsUnread(int $user, array $ids)
    {
        $key  = $this->getKey($user);
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {

            /** @var DataTableCachedResults $records */
            $records = $item->get();

            array_walk($records->data, function (array &$record) use ($ids) {

                if (in_array($record[RecordsDataTable::COLUMN_ID] ?? null, $ids)) {
                    $row_class = $record[DataTableResults::DT_ROW_CLASS];

                    if (strpos($row_class, 'unread') === false) {
                        $row_class .= ' unread';
                    }

                    $record[DataTableResults::DT_ROW_CLASS] = trim($row_class);
                }
            });

            $item->set($records);
            $item->expiresAfter(Seconds::FIVE_MINUTES);

            $this->cache->save($item);
        }
    }

    /**
     * Returns cache item key for specified user.
     *
     * @param   int $user
     *
     * @return  string
     */
    protected function getKey(int $user)
    {
        $key = sprintf('%s+%d', RecordsDataTable::class, $user);

        return md5($key);
    }
}
