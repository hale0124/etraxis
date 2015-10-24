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

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data to return to DataTables plugin.
 *
 * @see http://www.datatables.net/manual/server-side
 *
 * @property    int   $recordsTotal    Total records, before filtering.
 * @property    int   $recordsFiltered Total records, after filtering.
 * @property    array $data            The data to be displayed in the table.
 */
class DataTableResults
{
    /**
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value = "0")
     */
    public $recordsTotal = 0;

    /**
     * @Assert\NotNull()
     * @Assert\GreaterThanOrEqual(value = "0")
     */
    public $recordsFiltered = 0;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type = "array")
     */
    public $data = [];
}
