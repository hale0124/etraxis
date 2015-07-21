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
 * Updates specified account.
 *
 * @property    int    $id          User ID.
 * @property    string $username    New login.
 * @property    string $fullname    New full name.
 * @property    string $email       New email address.
 * @property    string $description New description.
 * @property    bool   $admin       New role (whether has administrator permissions).
 * @property    bool   $disabled    New status.
 */
class UpdateUserCommand extends BaseCommand
{
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
     * @Assert\NotBlank()
     */
    public $admin = null;

    /**
     * @Assert\NotBlank()
     */
    public $disabled = null;
}
