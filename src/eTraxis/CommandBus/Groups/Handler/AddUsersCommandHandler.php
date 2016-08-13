<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Groups\AddUsersCommand;
use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddUsersCommandHandler
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
     * Adds specified accounts to group.
     *
     * @param   AddUsersCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(AddUsersCommand $command)
    {
        /** @var Group $group */
        $group = $this->manager->find(Group::class, $command->id);

        if (!$group) {
            throw new NotFoundHttpException('Unknown group.');
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('u')
            ->from(User::class, 'u')
            ->where($query->expr()->in('u.id', ':users'))
            ->setParameter('users', $command->users)
        ;

        /** @var User[] $users */
        $users = $query->getQuery()->getResult();

        foreach ($users as $user) {
            $group->addMember($user);
        }

        $this->manager->persist($group);
    }
}
