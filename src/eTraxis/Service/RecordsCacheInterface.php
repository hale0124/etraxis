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
use eTraxis\DataTables\DataTableCachedResults;

/**
 * Records cache interface.
 */
interface RecordsCacheInterface
{
    /**
     * Saves provided records data for specified user.
     *
     * @param   int                    $user
     * @param   DataTableCachedResults $records
     */
    public function saveRecords(int $user, DataTableCachedResults $records);

    /**
     * Retrieves saved records data, associated with specified user.
     * If records data is not found, or it doesn't correspond with specified request, FALSE is returned.
     *
     * @param   int            $user
     * @param   DataTableQuery $request
     *
     * @return  DataTableCachedResults|false
     */
    public function getRecords(int $user, DataTableQuery $request);

    /**
     * Deletes saved records data, associated with specified user.
     *
     * @param   int $user
     */
    public function deleteRecords(int $user);

    /**
     * Returns ID of the record previous to the specified one.
     *
     * @param   int $user
     * @param   int $id
     *
     * @return  int|false
     */
    public function getPrevious(int $user, int $id);

    /**
     * Returns ID of the record next to the specified one.
     *
     * @param   int $user
     * @param   int $id
     *
     * @return  int|false
     */
    public function getNext(int $user, int $id);

    /**
     * Marks specified cached records as read.
     *
     * @param   int   $user
     * @param   int[] $ids
     */
    public function markRecordsAsRead(int $user, array $ids);

    /**
     * Marks specified cached records as unread.
     *
     * @param   int   $user
     * @param   int[] $ids
     */
    public function markRecordsAsUnread(int $user, array $ids);
}
