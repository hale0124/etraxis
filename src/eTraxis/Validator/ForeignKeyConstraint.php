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

/**
 * A contraint to check that specified foreign key ID refers to existing database entity.
 *
 * @Annotation
 */
class ForeignKeyConstraint extends Constraint
{
    /** @var string Error message. */
    public $message = 'Unknown entity.';

    /** @var string Target entity name. */
    public $entity = null;

    /** @var string Name of property in the target entity. */
    public $property = 'id';

    /** @var bool Whether to accept NULL references (FALSE to accept). */
    public $required = true;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'foreign_key_validator';
    }
}
