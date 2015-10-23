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

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new project.
 *
 * Returns ID of the created project.
 *
 * @property    string $name        Project name.
 * @property    string $description Description.
 * @property    bool   $suspended   Status.
 */
class CreateProjectCommand
{
    use ObjectInitiationTrait;

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
