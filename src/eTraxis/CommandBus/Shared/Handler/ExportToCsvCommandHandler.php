<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Shared\Handler;

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\LineEnding;
use eTraxis\CommandBus\Shared\ExportToCsvCommand;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Command handler.
 */
class ExportToCsvCommandHandler
{
    /**
     * Exports specified data to CSV file.
     *
     * @param   ExportToCsvCommand $command
     *
     * @return  StreamedResponse Resulted stream response to send back to user.
     */
    public function handle(ExportToCsvCommand $command)
    {
        $response = new StreamedResponse(function () use ($command) {

            $delimiter = CsvDelimiter::getDelimiter($command->delimiter);
            $tail      = LineEnding::getLineEnding($command->tail);

            $callback = function ($item) use ($delimiter) {
                $count = 0;
                $item  = str_replace('"', '""', $item, $count);
                $pos   = mb_strpos($item, $delimiter);

                return ($count || $pos !== false) ? '"' . $item . '"' : $item;
            };

            foreach ($command->data as $row) {

                $result = implode($delimiter, array_map($callback, $row)) . $tail;

                if ($command->encoding != 'UTF-8') {
                    $result = iconv('UTF-8', $command->encoding . '//TRANSLIT', $result);
                }

                print($result);
            }
        });

        if (substr($command->filename, -4) != '.csv') {
            $command->filename .= '.csv';
        }

        if ($command->filename == '.csv') {
            $command->filename = 'eTraxis.csv';
        }

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $command->filename, 'eTraxis.csv');

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/csv');

        $response->setCharset($command->encoding);

        return $response;
    }
}
