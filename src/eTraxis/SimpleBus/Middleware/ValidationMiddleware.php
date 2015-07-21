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

namespace eTraxis\SimpleBus\Middleware;

use eTraxis\Exception\CommandException;
use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Middleware to validate a command before handle it.
 */
class ValidationMiddleware implements MessageBusMiddleware
{
    protected $logger;
    protected $validator;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface    $logger    Debug logger.
     * @param   ValidatorInterface $validator Validator service.
     */
    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger    = $logger;
        $this->validator = $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @param   \eTraxis\SimpleBus\BaseCommand $message
     *
     * @throws  CommandException
     */
    public function handle($message, callable $next)
    {
        $errors = $this->validator->validate($message);

        if (count($errors)) {

            foreach ($errors as $error) {
                $message->errors[$error->getPropertyPath()] = $error->getMessage();
            }

            $errmsg = implode("\n", $message->errors);
            $this->logger->error($errmsg, $message->errors);
            throw new CommandException($errmsg);
        }

        $next($message);
    }
}
