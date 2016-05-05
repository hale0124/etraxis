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
use Symfony\Component\Validator\Mapping\ClassMetadata;

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
     * @Assert\Regex("/^\d+$/")
     */
    public $id;

    /**
     * Validator annotations are ignored in traits, so reconfigure them evidently.
     *
     * @param   ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata
            ->addPropertyConstraint('id', new Assert\NotBlank())
            ->addPropertyConstraint('id', new Assert\Regex(['pattern' => '/^\d+$/']))
        ;
    }
}
