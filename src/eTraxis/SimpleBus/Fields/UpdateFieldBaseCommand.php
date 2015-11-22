<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base command to update specified field.
 *
 * @property    int    $id           Field ID.
 * @property    string $name         New field name.
 * @property    string $description  New description.
 * @property    bool   $required     Whether the field is required.
 * @property    bool   $guestAccess  Whether to grant view access to anonymous.
 * @property    bool   $showInEmails Whether to show the field in email notifications.
 */
class UpdateFieldBaseCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     */
    public $name;

    /**
     * @Assert\Length(max = "1000")
     */
    public $description;

    /**
     * @Assert\NotNull()
     */
    public $required;

    /**
     * @Assert\NotNull()
     */
    public $guestAccess;

    /**
     * @Assert\NotNull()
     */
    public $showInEmails;
}
