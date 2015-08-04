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

use eTraxis\CommandBus\Users\GetUserGroupsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class GetUserGroupsCommandHandler
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
     * Finds all groups the specified account belongs to.
     *
     * @param   GetUserGroupsCommand $command
     *
     * @return  \eTraxis\Entity\Group[]
     */
    public function handle(GetUserGroupsCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Group');

        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->join('g.users', 'u')
            ->leftJoin('g.project', 'p')
            ->where('u.id = :id')
            ->setParameter('id', $command->id)
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        return $query->getQuery()->getResult();
    }
}
