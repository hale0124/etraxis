<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\Export;

use SimpleBus\MessageTrait;
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
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     */
    public $filename;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\CsvDelimiter", "keys"})
     */
    public $delimiter;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\Encoding", "keys"})
     */
    public $encoding;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Dictionary\LineEnding", "keys"})
     */
    public $tail;
}
