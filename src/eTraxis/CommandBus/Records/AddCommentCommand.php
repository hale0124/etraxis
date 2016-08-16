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

use eTraxis\Traits\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Adds new comment to specified record.
 *
 * @property    int    $record  Record ID.
 * @property    string $text    Text of the comment.
 * @property    bool   $private Whether comment is private.
 */
class AddCommentCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $record;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="4000")
     */
    public $text;

    /**
     * @Assert\NotNull()
     */
    public $private;
}
