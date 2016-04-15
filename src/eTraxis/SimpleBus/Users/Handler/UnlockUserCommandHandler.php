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
use eTraxis\SimpleBus\Users\UnlockUserCommand;

/**
 * Command handler.
 */
class UnlockUserCommandHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Clears locks count for specified eTraxis account.
     *
     * @param   UnlockUserCommand $command
     */
    public function handle(UnlockUserCommand $command)
    {
        /** @var User $user */
        if ($user = $this->manager->find(User::class, $command->id)) {

            $user->unlock();

            $this->manager->persist($user);
            $this->manager->flush();
        }
    }
}
