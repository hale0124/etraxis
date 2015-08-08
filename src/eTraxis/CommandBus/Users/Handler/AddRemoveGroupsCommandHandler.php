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

use eTraxis\CommandBus\Users\AddGroupsCommand;
use eTraxis\CommandBus\Users\RemoveGroupsCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveGroupsCommandHandler
{
    protected $logger;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     */
    public function __construct(LoggerInterface $logger, RegistryInterface $doctrine)
    {
        $this->logger   = $logger;
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
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        $user = $repository->find($command->id);

        if (!$user) {
            $this->logger->error('Unknown user.', [$command->id]);
            throw new NotFoundHttpException('Unknown user.');
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

            if ($command instanceof AddGroupsCommand) {
                $group->addUser($user);
            }
            elseif ($command instanceof RemoveGroupsCommand) {
                $group->removeUser($user);
            }

            $this->doctrine->getManager()->persist($group);
        }

        $this->doctrine->getManager()->flush();
    }
}
