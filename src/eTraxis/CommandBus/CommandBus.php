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

namespace eTraxis\CommandBus;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command Bus.
 */
class CommandBus implements CommandBusInterface
{
    protected $container;
    protected $logger;
    protected $validator;

    /** @var array List of registered command handlers. */
    protected $handlers = [];

    /**
     * Dependency Injection constructor.
     *
     * @param   ContainerInterface $container DI container.
     * @param   LoggerInterface    $logger    Debug logger.
     * @param   ValidatorInterface $validator Validator service.
     */
    public function __construct(
        ContainerInterface $container,
        LoggerInterface    $logger,
        ValidatorInterface $validator)
    {
        $this->container = $container;
        $this->logger    = $logger;
        $this->validator = $validator;
    }

    /**
     * Registers specified command handler.
     *
     * @param   string $id      Service ID of the command handler.
     * @param   string $command Class name of a command it handles.
     */
    public function addHandler($id, $command)
    {
        $this->handlers[$command] = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command)
    {
        $this->logger->debug('Handle message', [get_class($command)]);

        // Validate command before handle it.
        $violations = $this->validator->validate($command);

        if (count($violations)) {

            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $this->logger->error('Validation exception', $errors);
            throw new ValidationException($errors);
        }

        // Retrieve command handler.
        $service = $this->handlers[get_class($command)];
        $handler = $this->container->get($service);

        try {
            // Start timer.
            list($msec, $sec) = explode(' ', microtime());
            $timer_started    = (float) $msec + (float) $sec;

            // Handle command.
            $result = $handler->handle($command);
        }
        finally {
            // Stop timer.
            list($msec, $sec) = explode(' ', microtime());
            $timer_stopped    = (float) $msec + (float) $sec;

            $this->logger->debug('Message processing time', [$timer_stopped - $timer_started]);
        }

        return $result;
    }
}
