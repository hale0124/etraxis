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

use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\AddGroupsCommand;
use eTraxis\SimpleBus\Users\RemoveGroupsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveGroupsCommandHandler
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
     * Adds account to specified groups.
     *
     * @param   AddGroupsCommand|RemoveGroupsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        $user = $repository->find($command->id);

        if (!$user) {
            throw new NotFoundHttpException('Unknown user.');
        }

        $repository = $this->doctrine->getRepository(Group::class);

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->where($query->expr()->in('g.id', ':groups'))
            ->setParameter('groups', $command->groups)
        ;

        /** @var Group[] $groups */
        $groups = $query->getQuery()->getResult();

        foreach ($groups as $group) {

            if ($command instanceof AddGroupsCommand) {
                $group->addMember($user);
            }
            elseif ($command instanceof RemoveGroupsCommand) {
                $group->removeMember($user);
            }

            $this->doctrine->getManager()->persist($group);
        }

        $this->doctrine->getManager()->flush();
        $this->doctrine->getManager()->refresh($user);
    }
}
