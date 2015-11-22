<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Creates new "date" field.
 *
 * @property    int $minValue Minimum allowed value.
 * @property    int $maxValue Maximum allowed value.
 * @property    int $default  Default value of the field.
 */
class CreateDateFieldCommand extends CreateFieldBaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "int")
     * @Assert\Range(min = "-2147483648", max = "2147483647")
     */
    public $minValue;

    /**
     * @Assert\NotBlank()
     * @Assert\Type(type = "int")
     * @Assert\Range(min = "-2147483648", max = "2147483647")
     */
    public $maxValue;

    /**
     * @Assert\Type(type = "int")
     * @Assert\Range(min = "-2147483648", max = "2147483647")
     */
    public $default;
}
