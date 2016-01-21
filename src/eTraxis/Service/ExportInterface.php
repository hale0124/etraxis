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

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export interface.
 */
interface ExportInterface
{
    /**
     * Exports specified data to CSV file.
     *
     * @param   ExportCsvQuery $query Export parameters.
     * @param   array          $data  Data to output.
     *
     * @return  StreamedResponse Resulted stream response to send back to user.
     */
    public function exportCsv(ExportCsvQuery $query, $data = []);
}
