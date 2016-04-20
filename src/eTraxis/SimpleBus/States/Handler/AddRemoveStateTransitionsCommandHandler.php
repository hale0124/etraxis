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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateRoleTransition;
use eTraxis\SimpleBus\States\AddStateTransitionsCommand;
use eTraxis\SimpleBus\States\RemoveStateTransitionsCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveStateTransitionsCommandHandler
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
     * Manages transitions from specified state.
     *
     * @param   AddStateTransitionsCommand|RemoveStateTransitionsCommand $command
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

        /** @var State[] $transitions */
        $transitions = $this->manager->getRepository(State::class)->findBy([
            'template' => $state->getTemplate(),
            'id'       => $command->transitions,
        ])
        ;

        if (SystemRole::has($command->group)) {

            $query = $this->manager->createQuery('
                DELETE eTraxis:StateRoleTransition t
                WHERE t.fromState = :state
                AND t.toState IN (:transitions)
                AND t.role = :role
            ');

            $query->execute([
                'state'       => $state,
                'transitions' => $transitions,
                'role'        => $command->group,
            ]);

            if ($command instanceof AddStateTransitionsCommand) {

                foreach ($transitions as $transition) {

                    $entity = new StateRoleTransition();

                    $entity
                        ->setFromState($state)
                        ->setToState($transition)
                        ->setRole($command->group)
                    ;

                    $this->manager->persist($entity);
                }
            }
        }
        else {

            /** @var Group $group */
            $group = $this->manager->find(Group::class, $command->group);

            if (!$group) {
                throw new NotFoundHttpException('Unknown group.');
            }

            $query = $this->manager->createQuery('
                DELETE eTraxis:StateGroupTransition t
                WHERE t.fromState = :state
                AND t.toState IN (:transitions)
                AND t.group = :group
            ');

            $query->execute([
                'state'       => $state,
                'transitions' => $transitions,
                'group'       => $group,
            ]);

            if ($command instanceof AddStateTransitionsCommand) {

                foreach ($transitions as $transition) {

                    $entity = new StateGroupTransition();

                    $entity
                        ->setFromState($state)
                        ->setToState($transition)
                        ->setGroup($group)
                    ;

                    $this->manager->persist($entity);
                }
            }
        }
    }
}
