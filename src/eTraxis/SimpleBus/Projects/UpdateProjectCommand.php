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

use SimpleBus\MessageTrait;
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
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

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
