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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Group;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\AddGroupsCommand;
use eTraxis\SimpleBus\Users\RemoveGroupsCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveGroupsCommandHandler
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
     * Adds account to specified groups.
     *
     * @param   AddGroupsCommand|RemoveGroupsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var User $user */
        $user = $this->manager->find(User::class, $command->id);

        if (!$user) {
            throw new NotFoundHttpException('Unknown user.');
        }

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('g')
            ->from(Group::class, 'g')
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

            $this->manager->persist($group);
        }
    }
}
