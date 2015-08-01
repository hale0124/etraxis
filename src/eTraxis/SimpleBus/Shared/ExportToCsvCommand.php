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

namespace eTraxis\SimpleBus\Shared;

use eTraxis\SimpleBus\BaseCommand;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Exports specified data to CSV file.
 *
 * Returns prepared stream response.
 *
 * @property    string $filename  Suggested file name.
 * @property    string $delimiter CSV delimiter.
 * @property    string $encoding  CSV encoding.
 * @property    string $tail      CSV line endings.
 * @property    array  $data      Data to output.
 */
class ExportToCsvCommand extends BaseCommand
{
    /**
     * @Assert\NotBlank()
     */
    public $filename = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\CsvDelimiter", "getAllKeys"})
     */
    public $delimiter = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Encoding", "getAllKeys"})
     */
    public $encoding = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\LineEnding", "getAllKeys"})
     */
    public $tail = null;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type = "array")
     * @Assert\All({
     *     @Assert\Type(type = "array")
     * })
     */
    public $data = [];
}
