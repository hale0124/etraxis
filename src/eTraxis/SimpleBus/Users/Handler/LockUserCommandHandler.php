<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\LockUserCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class LockUserCommandHandler
{
    protected $logger;
    protected $doctrine;

    protected $security_auth_attempts;
    protected $security_lock_time;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     * @param   int               $security_auth_attempts
     * @param   int               $security_lock_time
     */
    public function __construct(
        LoggerInterface   $logger,
        RegistryInterface $doctrine,
        $security_auth_attempts,
        $security_lock_time)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;

        $this->security_auth_attempts = $security_auth_attempts;
        $this->security_lock_time     = $security_lock_time;
    }

    /**
     * Increases locks count for specified eTraxis account.
     *
     * @param   LockUserCommand $command
     */
    public function handle(LockUserCommand $command)
    {
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneBy(['username' => $command->username . '@eTraxis'])) {

            if ($this->security_auth_attempts && $this->security_lock_time) {

                $user->setAuthAttempts($user->getAuthAttempts() + 1);

                if ($user->getAuthAttempts() === $this->security_auth_attempts) {

                    $this->logger->info('Lock the account', [$this->security_lock_time]);

                    $user->setAuthAttempts(0);
                    $user->setLockedUntil(time() + $this->security_lock_time * 60);
                }

                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->flush();
            }
        }
    }
}
