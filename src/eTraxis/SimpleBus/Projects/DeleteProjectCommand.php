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
 * Deletes specified project.
 *
 * @property    int $id Project ID.
 */
class DeleteProjectCommand
{
    use MessageTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $id;
}
