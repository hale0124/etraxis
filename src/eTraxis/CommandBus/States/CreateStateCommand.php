<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\States;

use eTraxis\Traits\CommandTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new state.
 *
 * @property    int    $template     ID of the state's template.
 * @property    string $name         State name.
 * @property    string $abbreviation State abbreviation.
 * @property    int    $type         Type of the state.
 * @property    int    $responsible  Type of responsibility management.
 * @property    int    $nextState    ID of the state which is next by default.
 */
class CreateStateCommand
{
    use CommandTrait;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d+$/")
     */
    public $template;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="3")
     */
    public $abbreviation;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"eTraxis\Dictionary\StateType", "keys"})
     */
    public $type;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"eTraxis\Dictionary\StateResponsible", "keys"})
     */
    public $responsible;

    /**
     * @Assert\Regex("/^\d+$/")
     */
    public $nextState;
}
