<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Command;

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base command.
 * Contains properties which are common for all commands to create or update a field.
 *
 * @property    string $name         Field name.
 * @property    string $description  Description.
 * @property    bool   $required     Whether the field is required.
 * @property    bool   $guestAccess  Whether to grant view access to anonymous.
 * @property    bool   $showInEmails Whether to show the field in email notifications.
 */
class FieldCommand
{
    use MessageTrait;

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