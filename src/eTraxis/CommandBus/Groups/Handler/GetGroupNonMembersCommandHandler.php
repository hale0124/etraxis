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

namespace eTraxis\CommandBus\Groups\Handler;

use eTraxis\CommandBus\Groups\GetGroupNonMembersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class GetGroupNonMembersCommandHandler
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
     * Finds all accounts which doesn't belong to the specified group.
     *
     * @param   GetGroupNonMembersCommand $command
     *
     * @return  \eTraxis\Entity\User[]
     */
    public function handle(GetGroupNonMembersCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Group');

        if (!$repository->find($command->id)) {
            return [];
        }

        $repository = $this->doctrine->getRepository('eTraxis:User');

        // Find all accounts which belong to group.
        $subquery = $repository->createQueryBuilder('u');

        $subquery
            ->select('u.id')
            ->join('u.groups', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $command->id)
        ;

        $members = $subquery->getQuery()->getArrayResult();

        // Find all other accounts.
        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        if (count($members)) {

            $query
                ->where($query->expr()->notIn('u.id', ':members'))
                ->setParameter('members', $members)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
