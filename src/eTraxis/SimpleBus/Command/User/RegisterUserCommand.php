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


namespace eTraxis\SimpleBus\Command\User;

use eTraxis\SimpleBus\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Registers LDAP account in eTraxis database.
 *
 * Input properties:
 *
 * @property    string $username Username to register/find.
 * @property    string $fullname Display name to store/update.
 * @property    string $email    Email address to store/update.
 *
 * Output properties:
 *
 * @property    int $id ID of the registered user.
 */
class RegisterUserCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="112")
     */
    protected $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="64")
     */
    protected $fullname;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     * @Assert\Email()
     */
    protected $email;

    protected $id;
}
