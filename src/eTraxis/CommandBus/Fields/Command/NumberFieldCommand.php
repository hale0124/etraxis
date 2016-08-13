<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Fields\Command;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Create/update command for "number" field.
 *
 * @property    int $minValue     Minimum allowed value.
 * @property    int $maxValue     Maximum allowed value.
 * @property    int $defaultValue Default value of the field.
 */
class NumberFieldCommand extends FieldCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="-1000000000", max="1000000000")
     * @Assert\Regex("/^(\-|\+)?\d+$/")
     */
    public $minValue;

    /**
     * @Assert\NotBlank()
     * @Assert\Range(min="-1000000000", max="1000000000")
     * @Assert\Regex("/^(\-|\+)?\d+$/")
     */
    public $maxValue;

    /**
     * @Assert\Range(min="-1000000000", max="1000000000")
     * @Assert\Regex("/^(\-|\+)?\d+$/")
     */
    public $defaultValue;
}
