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
 * Updates specified "duration" field.
 *
 * @property    int $minValue Minimum allowed value.
 * @property    int $maxValue Maximum allowed value.
 * @property    int $default  Default value of the field.
 */
class UpdateDurationFieldCommand extends UpdateFieldBaseCommand
{
    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d{1,6}:[0-5][0-9]$/")
     */
    public $minValue;

    /**
     * @Assert\NotBlank()
     * @Assert\Regex("/^\d{1,6}:[0-5][0-9]$/")
     */
    public $maxValue;

    /**
     * @Assert\Regex("/^\d{1,6}:[0-5][0-9]$/")
     */
    public $default;
}
