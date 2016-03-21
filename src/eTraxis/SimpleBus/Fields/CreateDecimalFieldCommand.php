<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new "decimal" field.
 *
 * @property    float $minValue Minimum allowed value.
 * @property    float $maxValue Maximum allowed value.
 * @property    float $default  Default value of the field.
 */
class CreateDecimalFieldCommand extends CreateFieldBaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^(\-|\+)?\d{1,10}(\.\d{1,10})?$/")
     */
    public $minValue;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^(\-|\+)?\d{1,10}(\.\d{1,10})?$/")
     */
    public $maxValue;

    /**
     * @Assert\Regex("/^(\-|\+)?\d{1,10}(\.\d{1,10})?$/")
     */
    public $default;
}
