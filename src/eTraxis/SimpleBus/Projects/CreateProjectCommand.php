<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Projects;

use eTraxis\Traits\MessageTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new project.
 *
 * @property    string $name        Project name.
 * @property    string $description Description.
 * @property    bool   $suspended   Status.
 */
class CreateProjectCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="25")
     */
    public $name;

    /**
     * @Assert\Length(max="100")
     */
    public $description;

    /**
     * @Assert\NotNull()
     */
    public $suspended;
}
