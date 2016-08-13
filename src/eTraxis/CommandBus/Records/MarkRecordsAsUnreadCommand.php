<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Records;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Marks specified records as unread.
 *
 * @property    int   $user    User ID.
 * @property    int[] $records Record IDs.
 */
class MarkRecordsAsUnreadCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type="array")
     * @Assert\Count(min="1", max="100")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Regex("/^\d+$/")
     * })
     */
    public $records;
}
