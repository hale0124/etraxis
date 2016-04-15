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
use eTraxis\SimpleBus\States\RemoveStateAssigneesCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveStateAssigneesCommandHandler
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
     * Manages allowed assignees of specified state.
     *
     * @param   AddStateAssigneesCommand|RemoveStateAssigneesCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var State $state */
        $state = $this->manager->find(State::class, $command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $project = $state->getTemplate()->getProject();

        /** @var Group[] $groups */
        $groups = $this->manager->getRepository(Group::class)->findBy([
            'id' => $command->groups,
        ])
        ;

        $this->manager->beginTransaction();

        $query = $this->manager->createQuery('
            DELETE eTraxis:StateAssignee a
            WHERE a.state = :state
            AND a.group IN (:groups)
        ');

        $query->execute([
            'state'  => $state,
            'groups' => $groups,
        ]);

        if ($command instanceof AddStateAssigneesCommand) {

            foreach ($groups as $group) {

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

        $this->manager->flush();
        $this->manager->commit();
    }
}
