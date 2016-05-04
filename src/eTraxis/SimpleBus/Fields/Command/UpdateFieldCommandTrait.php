<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Command;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Command trait.
 * Contains properties which are common for all commands to update specified field.
 *
 * @property    int $id Field ID.
 */
trait UpdateFieldCommandTrait
{
    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;
}
