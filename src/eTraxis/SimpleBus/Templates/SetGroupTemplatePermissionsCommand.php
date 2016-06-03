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
 * Sets permissions of specified group to specified template.
 *
 * @property    int      $id          Template ID.
 * @property    int      $group       Group ID.
 * @property    string[] $permissions Permissions.
 */
class SetGroupTemplatePermissionsCommand
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
     * @Assert\Type(type="array")
     * @Assert\All({
     *     @Assert\NotBlank(),
     *     @Assert\Choice(callback={"eTraxis\Dictionary\TemplatePermission", "keys"})
     * })
     */
    public $permissions;
}
