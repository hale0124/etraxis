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
 * Finds specified project.
 *
 * Returns found project object.
 *
 * @property    int $id Project ID.
 */
class FindProjectCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @eTraxis\Validator\EntityIdConstraint()
     */
    public $id;
}
