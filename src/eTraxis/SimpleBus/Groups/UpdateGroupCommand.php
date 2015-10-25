<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified group.
 *
 * @property    int    $id          Group ID.
 * @property    string $name        New name.
 * @property    string $description New description.
 */
class UpdateGroupCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "25")
     */
    public $name = null;

    /**
     * @Assert\Length(max = "100")
     */
    public $description = null;
}
