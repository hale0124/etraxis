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

use eTraxis\SimpleBus\Users\LockUserCommand;
use eTraxis\SimpleBus\Users\UnlockUserCommand;
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
    protected $command_bus;

    /**
     * Dependency Injection constructor.
     *
     * @param   MessageBus $command_bus
     */
    public function __construct(MessageBus $command_bus)
    {
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

        $command = new UnlockUserCommand([
            'username' => $token->getUsername(),
        ]);

        $this->command_bus->handle($command);
    }

    /**
     * Callback for failed authentication event.
     *
     * @param   AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $command = new LockUserCommand([
            'username' => $token->getUsername(),
        ]);

        $this->command_bus->handle($command);
    }
}
