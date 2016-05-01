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
use eTraxis\Entity\StateAssignee;
use eTraxis\SimpleBus\States\AddStateAssigneesCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddStateAssigneesCommandHandler
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
     * Adds allowed assignees for specified state.
     *
     * @param   AddStateAssigneesCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(AddStateAssigneesCommand $command)
    {
        /** @var State $state */
        $state = $this->manager->find(State::class, $command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $project   = $state->getTemplate()->getProject();
        $assignees = $state->getAssigneeGroups();

        /** @var Group[] $groups */
        $groups = $this->manager->getRepository(Group::class)->findBy([
            'id' => $command->groups,
        ]);

        foreach ($groups as $group) {

            // Skip already present group.
            if (in_array($group, $assignees)) {
                continue;
            }

            // Group must be global or belong to the same project.
            if ($group->getProject() === null || $group->getProject() === $project) {

                $entity = new StateAssignee();

                $entity
                    ->setState($state)
                    ->setGroup($group)
                ;

                $this->manager->persist($entity);
            }
        }
    }
}
