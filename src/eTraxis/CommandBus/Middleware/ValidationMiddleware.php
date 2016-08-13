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

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Middleware to validate a message before handle it.
 */
class ValidationMiddleware implements MessageBusMiddleware
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
     * @param   mixed $message
     *
     * @throws  ValidationException
     */
    public function handle($message, callable $next)
    {
        $violations = $this->validator->validate($message);

        if (count($violations)) {

            $errors = [];

            /** @var \Symfony\Component\Validator\ConstraintViolationInterface[] $violations */
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new ValidationException($errors);
        }

        $next($message);
    }
}
