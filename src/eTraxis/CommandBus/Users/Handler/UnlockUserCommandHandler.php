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

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\Users\UnlockUserCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class UnlockUserCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Clears locks count for specified eTraxis account.
     *
     * @param   UnlockUserCommand $command
     */
    public function handle(UnlockUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        if ($user = $repository->find($command->id)) {

            $user->setAuthAttempts(0);
            $user->setLockedUntil(0);

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }
    }
}
