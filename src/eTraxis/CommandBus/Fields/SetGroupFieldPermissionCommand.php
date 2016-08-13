<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sets permission of specified group to specified field.
 *
 * @property    int    $id         Field ID.
 * @property    int    $group      Group ID.
 * @property    string $permission Permission.
 */
class SetGroupFieldPermissionCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $group;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"eTraxis\Dictionary\FieldPermission", "keys"})
     */
    public $permission;
}
