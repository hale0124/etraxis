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

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * A validator for foreign key contraint.
 */
class ForeignKeyConstraintValidator extends ConstraintValidator
{
    protected $logger;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     */
    public function __construct(LoggerInterface $logger, RegistryInterface $doctrine)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        // Empty value is valid if not required.
        if (strlen($value) == 0) {
            if ($constraint->required) {
                $this->logger->error('The value is required.', [$value]);
                $this->context->addViolation($constraint->message);
            }
        }
        // Non-empty value may contain digits only.
        elseif (!ctype_digit($value)) {
            $this->logger->error('The value is invalid.', [$value]);
            $this->context->addViolation($constraint->message);
        }
        // If entity is specified, check that DB contains an entry with the value as its PK.
        elseif ($constraint->entity) {
            $repository = $this->doctrine->getRepository($constraint->entity);

            if (!$repository->findBy([$constraint->property => $value])) {
                $this->logger->error('Unknown entity.', [$value, $constraint->entity, $constraint->property]);
                $this->context->addViolation($constraint->message);
            }
        }
    }
}
