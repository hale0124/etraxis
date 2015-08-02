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

use eTraxis\SimpleBus\BaseCommand;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registers LDAP account in eTraxis database.
 *
 * Returns ID of the registered user.
 *
 * @property    string $username Username to register/find.
 * @property    string $fullname Display name to store/update.
 * @property    string $email    Email address to store/update.
 * @property    int    $result   ID of the registered user.
 */
class RegisterUserCommand extends BaseCommand
{
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
}
