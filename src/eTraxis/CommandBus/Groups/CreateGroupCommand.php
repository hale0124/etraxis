<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups;

use eTraxis\Traits\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new group.
 *
 * @property    int    $project     ID of the group's project (empty for global group).
 * @property    string $name        Group name.
 * @property    string $description Description.
 */
class CreateGroupCommand
{
    use CommandTrait;

    /**
     * @Assert\Regex("/^\d+$/")
     */
    public $project;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="25")
     */
    public $name;

    /**
     * @Assert\Length(max="100")
     */
    public $description;
}
