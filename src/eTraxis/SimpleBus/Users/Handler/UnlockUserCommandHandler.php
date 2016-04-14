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
use eTraxis\SimpleBus\Users\UnlockUserCommand;
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
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->find($command->id)) {

            $user->unlock();

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();
        }
    }
}
