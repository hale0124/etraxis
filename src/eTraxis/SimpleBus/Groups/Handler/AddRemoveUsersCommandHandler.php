<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Groups\AddUsersCommand;
use eTraxis\SimpleBus\Groups\RemoveUsersCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveUsersCommandHandler
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
     * Adds or removes specified accounts to/from group.
     *
     * @param   AddUsersCommand|RemoveUsersCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
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

            if ($command instanceof AddUsersCommand) {
                $group->addMember($user);
            }
            elseif ($command instanceof RemoveUsersCommand) {
                $group->removeMember($user);
            }
        }

        $this->manager->persist($group);
    }
}
