<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace Symfony\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * A validator for entity ID constraint.
 */
class EntityIdValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // Empty value is valid. Non-empty value may contain digits only.
        if (strlen($value) != 0 && !ctype_digit("{$value}")) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->context->addViolation($constraint->message);
        }
    }
}
