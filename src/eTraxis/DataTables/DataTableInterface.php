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

/**
 * DataTable service to work with entities of particular type.
 */
interface DataTableInterface
{
    /**
     * Handles specified DataTable request.
     *
     * @param   DataTableQuery $request
     *
     * @return  DataTableResults
     */
    public function handle(DataTableQuery $request);
}
