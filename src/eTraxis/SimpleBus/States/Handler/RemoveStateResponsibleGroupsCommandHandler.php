<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\States\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\SimpleBus\States\RemoveStateResponsibleGroupsCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class RemoveStateResponsibleGroupsCommandHandler
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
     * Removes allowed responsible groups for specified state.
     *
     * @param   RemoveStateResponsibleGroupsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(RemoveStateResponsibleGroupsCommand $command)
    {
        /** @var State $state */
        $state = $this->manager->find(State::class, $command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        // Responsible groups are applicable for assignable states only.
        if ($state->getResponsible() === State::RESPONSIBLE_ASSIGN) {

            /** @var Group[] $groups */
            $groups = $this->manager->getRepository(Group::class)->findBy([
                'id' => $command->groups,
            ]);

            $query = $this->manager->createQuery('
                DELETE eTraxis:StateResponsibleGroup srg
                WHERE srg.state = :state
                AND srg.group IN (:groups)
            ');

            $query->execute([
                'state'  => $state,
                'groups' => $groups,
            ]);
        }
    }
}
