<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Base command to create new field.
 *
 * @property    int    $template     ID of the field's template.
 * @property    int    $state        ID of the field's state.
 * @property    string $name         Field name.
 * @property    string $description  Description.
 * @property    bool   $required     Whether the field is required.
 * @property    bool   $guestAccess  Whether to grant view access to anonymous.
 * @property    bool   $showInEmails Whether to show the field in email notifications.
 */
class CreateFieldBaseCommand
{
    use MessageTrait;

    /**
     * @Assert\EntityId()
     * @Assert\Expression("value != false || this.state != false")
     */
    public $template;

    /**
     * @Assert\EntityId()
     * @Assert\Expression("value != false || this.template != false")
     */
    public $state;

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
