<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sets password for specified account.
 *
 * @property    int    $id       User ID.
 * @property    string $password New password.
 */
class SetPasswordCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     */
    public $password;
}
