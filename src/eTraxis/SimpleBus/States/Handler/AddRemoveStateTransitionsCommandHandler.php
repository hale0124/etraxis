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

use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\StateGroupTransition;
use eTraxis\Entity\StateRoleTransition;
use eTraxis\SimpleBus\States\AddStateTransitionsCommand;
use eTraxis\SimpleBus\States\RemoveStateTransitionsCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveStateTransitionsCommandHandler
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
     * Manages transitions from specified state.
     *
     * @param   AddStateTransitionsCommand|RemoveStateTransitionsCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->find($command->id);

        if (!$state) {
            throw new NotFoundHttpException('Unknown state.');
        }

        /** @var State[] $transitions */
        $transitions = $this->doctrine->getRepository(State::class)->findBy([
            'templateId' => $state->getTemplateId(),
            'id'         => $command->transitions,
        ]);

        $ids = [];

        foreach ($transitions as $transition) {
            $ids[] = $transition->getId();
        }

        if (count($ids) === 0) {
            return;
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->beginTransaction();

        if (array_key_exists($command->group, SystemRole::getCollection())) {

            $query = $em->createQuery('
                DELETE eTraxis:StateRoleTransition t
                WHERE t.fromStateId = :state
                AND t.role = :role
                AND t.toStateId IN (:ids)
            ');

            $query->execute([
                'ids'   => $ids,
                'state' => $state->getId(),
                'role'  => $command->group,
            ]);

            if ($command instanceof AddStateTransitionsCommand) {

                foreach ($transitions as $transition) {

                    $entity = new StateRoleTransition();

                    $entity
                        ->setFromStateId($state->getId())
                        ->setToStateId($transition->getId())
                        ->setRole($command->group)
                        ->setFromState($state)
                        ->setToState($transition)
                    ;

                    $em->persist($entity);
                }
            }
        }
        else {

            /** @var Group $group */
            $group = $this->doctrine->getRepository(Group::class)->find($command->group);

            if (!$group) {
                $em->rollback();
                throw new NotFoundHttpException('Unknown group.');
            }

            $query = $em->createQuery('
                DELETE eTraxis:StateGroupTransition t
                WHERE t.fromStateId = :state
                AND t.groupId = :group
                AND t.toStateId IN (:ids)
            ');

            $query->execute([
                'ids'   => $ids,
                'state' => $state->getId(),
                'group' => $group->getId(),
            ]);

            if ($command instanceof AddStateTransitionsCommand) {

                foreach ($transitions as $transition) {

                    $entity = new StateGroupTransition();

                    $entity
                        ->setFromStateId($state->getId())
                        ->setToStateId($transition->getId())
                        ->setGroupId($group->getId())
                        ->setFromState($state)
                        ->setToState($transition)
                        ->setGroup($group)
                    ;

                    $em->persist($entity);
                }
            }
        }

        $em->flush();
        $em->commit();
    }
}
