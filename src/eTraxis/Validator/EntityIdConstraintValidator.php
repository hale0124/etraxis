<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * A validator for entity ID contraint.
 */
class EntityIdConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        /** @var EntityIdConstraint $constraint */

        // Empty value is valid if not required.
        if (strlen($value) == 0) {
            if ($constraint->required) {
                $this->context->addViolation($constraint->message);
            }
        }
        // Non-empty value may contain digits only.
        elseif (!ctype_digit($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
