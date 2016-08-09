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

/**
 * DataTables cached results.
 */
class DataTableCachedResults
{
    /** @var int Total number of items. */
    public $total;

    /** @var array Items data. */
    public $data;

    /** @var string Query footprint. */
    protected $footprint;

    /**
     * Initializes object.
     *
     * @param   DataTableQuery $query
     * @param   int            $total
     * @param   array          $data
     */
    public function __construct(DataTableQuery $query, int $total = 0, array $data = [])
    {
        $this->total = $total;
        $this->data  = $data;

        $this->footprint = $this->getFootprint($query);
    }

    /**
     * Checks whether cached results correspond to specified query.
     *
     * @param   DataTableQuery $query
     *
     * @return  bool
     */
    public function isHit(DataTableQuery $query)
    {
        return $this->getFootprint($query) === $this->footprint;
    }

    /**
     * Calculates footprint of the specified query.
     *
     * @param   DataTableQuery $query
     *
     * @return  string
     */
    protected function getFootprint(DataTableQuery $query)
    {
        $footprint = sprintf('%s:%s:%s',
            json_encode($query->search),
            json_encode($query->order),
            json_encode($query->columns)
        );

        return md5($footprint);
    }
}
