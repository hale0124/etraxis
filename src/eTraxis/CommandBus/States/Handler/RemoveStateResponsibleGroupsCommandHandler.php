<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\States\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\States\RemoveStateResponsibleGroupsCommand;
use eTraxis\Dictionary\StateResponsible;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
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
        if ($state->getResponsible() === StateResponsible::ASSIGN) {

            /** @var Group[] $groups */
            $groups = $this->manager->getRepository(Group::class)->findBy([
                'id' => $command->groups,
            ]);

            $state->removeResponsibleGroups($groups);

            $this->manager->persist($state);
        }
    }
}
