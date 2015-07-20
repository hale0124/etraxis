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

use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\LockUserCommand;
use eTraxis\SimpleBus\Users\UnlockUserCommand;
use Psr\Log\LoggerInterface;
use SimpleBus\Message\Bus\MessageBus;
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
     * @param   LoggerInterface $logger
     * @param   MessageBus      $command_bus
     */
    public function __construct(LoggerInterface $logger, MessageBus $command_bus)
    {
        $this->logger      = $logger;
        $this->command_bus = $command_bus;
    }

    /**
     * {@inheritDoc}
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
