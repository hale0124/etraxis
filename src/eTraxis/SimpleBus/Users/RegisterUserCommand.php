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

namespace eTraxis\SimpleBus\Users;

use eTraxis\Traits\InitializationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registers LDAP account in eTraxis database.
 *
 * @property    string $username Username to register/find.
 * @property    string $fullname Display name to store/update.
 * @property    string $email    Email address to store/update.
 */
class RegisterUserCommand
{
    use InitializationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
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

    /** @var int ID of the registered user. */
    public $id = null;
}
