<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Clears locks count for specified eTraxis account.
 *
 * @property    int $id User ID.
 */
class UnlockUserCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;
}
