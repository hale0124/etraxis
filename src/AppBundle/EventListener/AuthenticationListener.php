<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\CommandBus\CommandBusInterface;
use eTraxis\CommandBus\Users\LockUserCommand;
use eTraxis\CommandBus\Users\UnlockUserCommand;
use eTraxis\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Authentication events listener.
 */
class AuthenticationListener implements EventSubscriberInterface
{
    protected $logger;
    protected $command_bus;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface     $logger
     * @param   CommandBusInterface $command_bus
     */
    public function __construct(LoggerInterface $logger, CommandBusInterface $command_bus)
    {
        $this->logger      = $logger;
        $this->command_bus = $command_bus;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    /**
     * Callback for successful authentication event.
     *
     * @param   AuthenticationEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $user = $token->getUser();

        if ($user instanceof User) {

            $this->logger->info('Authentication success', [$token->getUsername()]);

            $command = new UnlockUserCommand([
                'id' => $user->getId(),
            ]);

            $this->command_bus->handle($command);
        }
    }

    /**
     * Callback for failed authentication event.
     *
     * @param   AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $this->logger->info('Authentication failure', [$token->getUsername()]);

        $command = new LockUserCommand([
            'username' => $token->getUsername(),
        ]);

        $this->command_bus->handle($command);
    }
}
