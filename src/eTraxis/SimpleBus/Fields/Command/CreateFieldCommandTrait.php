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
 * Contains properties which are common for all commands to create new field.
 *
 * @property    int $state ID of the field's state.
 */
trait CreateFieldCommandTrait
{
    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $state;
}
