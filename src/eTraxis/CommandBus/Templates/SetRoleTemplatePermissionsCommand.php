<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Templates;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sets permissions of specified role to specified template.
 *
 * @property    int      $id          Template ID.
 * @property    string   $role        System role.
 * @property    string[] $permissions Permissions.
 */
class SetRoleTemplatePermissionsCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"eTraxis\Dictionary\SystemRole", "keys"})
     */
    public $role;

    /**
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Choice(callback={"eTraxis\Dictionary\TemplatePermission", "keys"})
     * })
     */
    public $permissions;
}
