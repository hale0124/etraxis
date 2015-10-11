<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use eTraxis\CommandBus\Groups\GetGroupMembersCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class GetGroupMembersCommandHandler
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
     * Finds all accounts which belong to the specified group.
     *
     * @param   GetGroupMembersCommand $command
     *
     * @return  \eTraxis\Entity\User[]
     */
    public function handle(GetGroupMembersCommand $command)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        $query = $repository->createQueryBuilder('u');

        $query
            ->select('u')
            ->join('u.groups', 'g')
            ->where('g.id = :id')
            ->setParameter('id', $command->id)
            ->orderBy('u.fullname')
            ->addOrderBy('u.username')
        ;

        return $query->getQuery()->getResult();
    }
}
