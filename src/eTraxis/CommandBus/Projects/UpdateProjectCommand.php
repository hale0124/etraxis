<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Projects;

use eTraxis\Traits\CommandBusTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified project.
 *
 * @property    int    $id          Project ID.
 * @property    string $name        New name.
 * @property    string $description New description.
 * @property    bool   $suspended   New status.
 */
class UpdateProjectCommand
{
    use CommandBusTrait;

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

    /**
     * @Assert\NotNull()
     */
    public $suspended = null;
}
