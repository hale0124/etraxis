<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates;

use SimpleBus\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Grants specified group with permissions to specified template.
 *
 * @property    int $id          Template ID.
 * @property    int $group       Group ID or system role.
 * @property    int $permissions Permissions.
 */
class AddTemplatePermissionsCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Any({
     *     @Assert\EntityId(),
     *     @Assert\Choice(callback = {"eTraxis\Dictionary\SystemRole", "keys"})
     * })
     */
    public $group;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "int")
     */
    public $permissions;
}
