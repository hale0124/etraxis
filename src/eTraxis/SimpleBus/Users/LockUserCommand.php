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

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Increases locks count for specified eTraxis account.
 *
 * @property    string $username Username to lock.
 */
class LockUserCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="112")
     * @Assert\Regex(pattern="/^[a-z0-9_\.\-]+$/i", message="user.invalid.username");
     */
    public $username;
}
