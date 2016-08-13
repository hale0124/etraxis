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
use Symfony\Component\Validator\Mapping\ClassMetadata;

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
     * @Assert\Regex("/^\d+$/")
     */
    public $state;

    /**
     * Validator annotations are ignored in traits, so reconfigure them evidently.
     *
     * @param   ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint('state', new Assert\NotBlank())
            ->addPropertyConstraint('state', new Assert\Regex(['pattern' => '/^\d+$/']))
        ;
    }
}
