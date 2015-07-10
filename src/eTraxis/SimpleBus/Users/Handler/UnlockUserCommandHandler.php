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

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\Users\UnlockUserCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Clears locks count for specified eTraxis account.
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
     * {@inheritDoc}
     */
    public function handle(UnlockUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Model\User $user */
        if ($user = $repository->findOneBy(['username' => $command->username . '@eTraxis'])) {

            $user->setAuthAttempts(0);
            $user->setLockedUntil(0);

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }
    }
}
