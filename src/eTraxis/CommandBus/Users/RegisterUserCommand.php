<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registers LDAP account in eTraxis database.
 *
 * Returns ID of the registered user.
 *
 * @property    string $username Username to register/find.
 * @property    string $fullname Display name to store/update.
 * @property    string $email    Email address to store/update.
 */
class RegisterUserCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     * @Assert\Regex(pattern="/^[a-z0-9_\.\-]+$/i", message="user.invalid.username");
     */
    public $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "64")
     */
    public $fullname;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     * @Assert\Email()
     */
    public $email;
}
