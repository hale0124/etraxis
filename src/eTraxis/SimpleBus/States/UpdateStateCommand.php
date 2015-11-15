<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States;

use eTraxis\Traits\ObjectInitiationTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Updates specified state.
 *
 * @property    int    $id           State ID.
 * @property    string $name         New state name.
 * @property    string $abbreviation New state abbreviation.
 * @property    int    $responsible  New type of responsibility management.
 * @property    int    $nextState    ID of the state which is next by default.
 */
class UpdateStateCommand
{
    use ObjectInitiationTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\EntityId()
     */
    public $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max = "50")
     */
    public $abbreviation;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback = {"eTraxis\Collection\StateResponsible", "getAllKeys"})
     */
    public $responsible;

    /**
     * @Assert\EntityId()
     */
    public $nextState;
}
