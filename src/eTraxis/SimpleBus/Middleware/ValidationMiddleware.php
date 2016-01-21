<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Middleware;

use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Middleware to validate a message before handle it.
 */
class ValidationMiddleware implements MessageBusMiddleware
{
    protected $logger;
    protected $validator;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface    $logger
     * @param   ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger    = $logger;
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

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $this->logger->error('Validation exception', $errors);
            throw new ValidationException($errors);
        }

        $next($message);
    }
}
