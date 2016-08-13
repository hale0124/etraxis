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
 * Creates new account.
 *
 * @property    string $username    Login.
 * @property    string $fullname    Full name.
 * @property    string $email       Email address.
 * @property    string $description Description.
 * @property    string $password    Password.
 * @property    string $locale      Locale.
 * @property    string $theme       Theme.
 * @property    string $timezone    Timezone.
 * @property    bool   $admin       Role (whether has administrator permissions).
 * @property    bool   $disabled    Status.
 */
class CreateUserCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="112")
     * @Assert\Regex(pattern="/^[a-z0-9_\.\-]+$/i", message="user.invalid.username");
     */
    public $username;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="64")
     */
    public $fullname;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\Length(max="100")
     */
    public $description;

    /**
     * @Assert\NotBlank()
     */
    public $password;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Locale", "keys"})
     */
    public $locale;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Theme", "keys"})
     */
    public $theme;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback={"eTraxis\Dictionary\Timezone", "values"})
     */
    public $timezone;

    /**
     * @Assert\NotNull()
     */
    public $admin;

    /**
     * @Assert\NotNull()
     */
    public $disabled;
}
