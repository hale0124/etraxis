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
 * Creates new account.
 *
 * Returns ID of the created user.
 *
 * @property    string $username    Login.
 * @property    string $fullname    Full name.
 * @property    string $email       Email address.
 * @property    string $description Description.
 * @property    string $password    Password.
 * @property    string $locale      Locale.
 * @property    string $theme       Theme.
 * @property    int    $timezone    Timezone.
 * @property    bool   $admin       Role (whether has administrator permissions).
 * @property    bool   $disabled    Status.
 */
class CreateUserCommand extends BaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "112")
     */
    public $username = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "64")
     */
    public $fullname = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     * @Assert\Email()
     */
    public $email = null;

    /**
     * @Assert\Length(max = "100")
     */
    public $description = null;

    /**
     * @Assert\NotBlank()
     */
    public $password = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Model\LocaleStaticCollection", "getAllKeys"})
     */
    public $locale = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Model\ThemeStaticCollection", "getAllKeys"})
     */
    public $theme = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Model\TimezoneStaticCollection", "getAllKeys"})
     */
    public $timezone = null;

    /**
     * @Assert\NotNull()
     */
    public $admin = null;

    /**
     * @Assert\NotNull()
     */
    public $disabled = null;
}
