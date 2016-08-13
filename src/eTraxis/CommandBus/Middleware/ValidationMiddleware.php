<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Middleware;

use League\Tactician\Middleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Middleware to validate a command before handle it.
 */
class ValidationMiddleware implements Middleware
{
    protected $validator;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  ValidationException
     */
    public function execute($command, callable $next)
    {
        $violations = $this->validator->validate($command);

        if (count($violations)) {

            $errors = [];

            /** @var \Symfony\Component\Validator\ConstraintViolationInterface[] $violations */
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ValidationException($errors);
        }

        return $next($command);
    }
}
