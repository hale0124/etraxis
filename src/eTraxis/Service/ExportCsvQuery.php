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

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A query to export to CSV file.
 *
 * @property    string $filename  Suggested file name.
 * @property    string $delimiter CSV delimiter.
 * @property    string $encoding  CSV encoding.
 * @property    string $tail      CSV line endings.
 */
class ExportCsvQuery
{
    use ObjectInitiationTrait;

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
}
