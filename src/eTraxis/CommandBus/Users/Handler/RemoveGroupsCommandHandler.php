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

use eTraxis\CommandBus\Users\RemoveGroupsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class RemoveGroupsCommandHandler
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
     * Removes account from specified groups.
     *
     * @param   RemoveGroupsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(RemoveGroupsCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        $user = $repository->find($command->id);

        if (!$user) {
            throw new NotFoundHttpException();
        }

        $repository = $this->doctrine->getRepository('eTraxis:Group');

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->where($query->expr()->in('g.id', ':groups'))
            ->setParameter('groups', $command->groups)
        ;

        /** @var \eTraxis\Entity\Group[] $groups */
        $groups = $query->getQuery()->getResult();

        foreach ($groups as $group) {
            $group->removeUser($user);
            $this->doctrine->getManager()->persist($group);
        }

        $this->doctrine->getManager()->flush();
    }
}
