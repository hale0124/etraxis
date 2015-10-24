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

use Symfony\Component\HttpFoundation\Request;

/**
 * DataTables factory.
 */
interface DataTablesFactoryInterface
{
    /**
     * Handles specified DataTable request.
     *
     * @param   Request $request Original request.
     * @param   string  $entity  Entity name which this DataTable works with (e.g. "eTraxis:Issue").
     *
     * @return  array Data to return in JSON response.
     *
     * @throws  DataTableException
     */
    public function handle(Request $request, $entity);
}
