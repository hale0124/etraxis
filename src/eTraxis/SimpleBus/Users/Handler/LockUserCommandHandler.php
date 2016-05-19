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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\LockUserCommand;
use Psr\Log\LoggerInterface;

/**
 * Command handler.
 */
class LockUserCommandHandler
{
    protected $logger;
    protected $manager;
    protected $security_auth_attempts;
    protected $security_lock_time;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface        $logger
     * @param   EntityManagerInterface $manager
     * @param   int                    $security_auth_attempts
     * @param   int                    $security_lock_time
     */
    public function __construct(
        LoggerInterface        $logger,
        EntityManagerInterface $manager,
        int                    $security_auth_attempts = null,
        int                    $security_lock_time = null)
    {
        $this->logger                 = $logger;
        $this->manager                = $manager;
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
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneBy(['username' => $command->username . '@eTraxis'])) {

            if ($this->security_auth_attempts && $this->security_lock_time) {

                if ($user->lock($this->security_auth_attempts, $this->security_lock_time)) {
                    $this->logger->info('Lock the account', [$this->security_lock_time]);
                }

                $this->manager->persist($user);
            }
        }
    }
}
