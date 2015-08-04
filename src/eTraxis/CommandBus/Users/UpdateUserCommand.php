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

namespace eTraxis\CommandBus\Users;

use eTraxis\Traits\CommandBusTrait;
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
 * @property    int    $timezone    New timezone.
 * @property    bool   $admin       New role (whether has administrator permissions).
 * @property    bool   $disabled    New status.
 */
class UpdateUserCommand
{
    use CommandBusTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(value = "0")
     */
    public $id;

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
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Locale", "getAllKeys"})
     */
    public $locale = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Theme", "getAllKeys"})
     */
    public $theme = null;

    /**
     * @Assert\NotNull()
     * @Assert\Choice(callback = {"eTraxis\Collection\Timezone", "getAllKeys"})
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
