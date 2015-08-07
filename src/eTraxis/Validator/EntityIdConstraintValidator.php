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
        // Empty value is valid. Non-empty value may contain digits only.
        if (strlen($value) != 0 && !ctype_digit("{$value}")) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->context->addViolation($constraint->message);
        }
    }
}
