<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\EventListener;

use eTraxis\CommandBus\Users\LockUserCommand;
use eTraxis\CommandBus\Users\UnlockUserCommand;
use eTraxis\Entity\User;
use League\Tactician\CommandBus;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;

/**
 * Locks/unlocks a user depending on result of its login attempt.
 */
class UsersLockout
{
    protected $logger;
    protected $commandbus;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface $logger
     * @param   CommandBus      $commandbus
     */
    public function __construct(LoggerInterface $logger, CommandBus $commandbus)
    {
        $this->logger     = $logger;
        $this->commandbus = $commandbus;
    }

    /**
     * Callback for successful authentication event.
     *
     * @param   AuthenticationEvent $event
     */
    public function onSuccess(AuthenticationEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $user = $token->getUser();

        if ($user instanceof User) {

            $this->logger->info('Authentication success', [$token->getUsername()]);

            $command = new UnlockUserCommand([
                'id' => $user->getId(),
            ]);

            $this->commandbus->handle($command);
        }
    }

    /**
     * Callback for failed authentication event.
     *
     * @param   AuthenticationFailureEvent $event
     */
    public function onFailure(AuthenticationFailureEvent $event)
    {
        $token = $event->getAuthenticationToken();

        if ($token->getUsername()) {

            $this->logger->info('Authentication failure', [$token->getUsername()]);

            $command = new LockUserCommand([
                'username' => $token->getUsername(),
            ]);

            $this->commandbus->handle($command);
        }
    }
}
