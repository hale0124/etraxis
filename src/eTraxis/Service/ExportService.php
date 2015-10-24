<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\LineEnding;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export service.
 */
class ExportService implements ExportInterface
{
    /**
     * {@inheritdoc}
     */
    public function exportCsv(ExportCsvQuery $query, $data = [])
    {
        $response = new StreamedResponse(function () use ($query, $data) {

            $delimiter = CsvDelimiter::getDelimiter($query->delimiter);
            $tail      = LineEnding::getLineEnding($query->tail);

            $callback = function ($item) use ($delimiter) {
                $count = 0;
                $item  = str_replace('"', '""', $item, $count);
                $pos   = mb_strpos($item, $delimiter);

                return ($count || $pos !== false) ? '"' . $item . '"' : $item;
            };

            foreach ($data as $row) {

                $result = implode($delimiter, array_map($callback, $row)) . $tail;

                if ($query->encoding != 'UTF-8') {
                    $result = iconv('UTF-8', $query->encoding . '//TRANSLIT', $result);
                }

                print($result);
            }
        });

        if (substr($query->filename, -4) != '.csv') {
            $query->filename .= '.csv';
        }

        if ($query->filename == '.csv') {
            $query->filename = 'eTraxis.csv';
        }

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $query->filename, 'eTraxis.csv');

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv');

        $response->setCharset($query->encoding);

        return $response;
    }
}
