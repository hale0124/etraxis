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

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified account.
 *
 * @property    int    $id          User ID.
 * @property    string $username    New login.
 * @property    string $fullname    New full name.
 * @property    string $email       New email address.
 * @property    string $description New description.
 * @property    string $locale      New locale.
 * @property    string $theme       New theme.
 * @property    string $timezone    New timezone.
 * @property    bool   $admin       New role (whether has administrator permissions).
 * @property    bool   $disabled    New status.
 */
class UpdateUserCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

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
