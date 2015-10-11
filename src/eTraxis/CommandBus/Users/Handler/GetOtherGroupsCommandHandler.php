<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\Users\GetOtherGroupsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class GetOtherGroupsCommandHandler
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
     * Finds all groups the specified account doesn't belong to.
     *
     * @param   GetOtherGroupsCommand $command
     *
     * @return  \eTraxis\Entity\Group[]
     */
    public function handle(GetOtherGroupsCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        if (!$repository->find($command->id)) {
            return [];
        }

        $repository = $this->doctrine->getRepository('eTraxis:Group');

        // Find all groups the account belong to.
        $subquery = $repository->createQueryBuilder('g');

        $subquery
            ->select('g.id')
            ->join('g.users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $command->id)
        ;

        $groups = $subquery->getQuery()->getArrayResult();

        // Find all other groups.
        $query = $repository->createQueryBuilder('g');

        $query
            ->select('g')
            ->addSelect('p')
            ->leftJoin('g.project', 'p')
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        if (count($groups)) {

            $query
                ->where($query->expr()->notIn('g.id', ':groups'))
                ->setParameter('groups', $groups)
            ;
        }

        return $query->getQuery()->getResult();
    }
}
