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
use eTraxis\SimpleBus\States\RemoveStateAssigneesCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class RemoveStateAssigneesCommandHandler
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
     * Removes allowed assignees for specified state.
     *
     * @param   RemoveStateAssigneesCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(RemoveStateAssigneesCommand $command)
    {
        /** @var State $state */
        $state = $this->manager->find(State::class, $command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        /** @var Group[] $groups */
        $groups = $this->manager->getRepository(Group::class)->findBy([
            'id' => $command->groups,
        ]);

        $query = $this->manager->createQuery('
            DELETE eTraxis:StateAssignee a
            WHERE a.state = :state
            AND a.group IN (:groups)
        ');

        $query->execute([
            'state'  => $state,
            'groups' => $groups,
        ]);
    }
}
