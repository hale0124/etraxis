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

use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Groups\AddUsersCommand;
use eTraxis\SimpleBus\Groups\RemoveUsersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveUsersCommandHandler
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
     * Adds or removes specified accounts to/from group.
     *
     * @param   AddUsersCommand|RemoveUsersCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(Group::class);

        /** @var Group $group */
        $group = $repository->find($command->id);

        if (!$group) {
            throw new NotFoundHttpException('Unknown group.');
        }

        $repository = $this->doctrine->getRepository(User::class);

        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
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

        $this->doctrine->getManager()->persist($group);
        $this->doctrine->getManager()->flush();
    }
}
