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

use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\StateAssignee;
use eTraxis\SimpleBus\States\AddStateAssigneesCommand;
use eTraxis\SimpleBus\States\RemoveStateAssigneesCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveStateAssigneesCommandHandler
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
     * Manages allowed assignees of specified state.
     *
     * @param   AddStateAssigneesCommand|RemoveStateAssigneesCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->find($command->id);

        if (!$state) {
            $this->logger->error('Unknown state.', [$command->id]);
            throw new NotFoundHttpException('Unknown state.');
        }

        $projectId = $state->getTemplate()->getProjectId();

        /** @var Group[] $groups */
        $groups = $this->doctrine->getRepository(Group::class)->findBy([
            'id' => $command->groups,
        ]);

        $ids = [];

        foreach ($groups as $group) {
            if ($group->getProjectId() === null || $group->getProjectId() == $projectId) {
                $ids[] = $group->getId();
            }
        }

        if (count($ids) == 0) {
            return;
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->beginTransaction();

        $query = $em->createQuery('
            DELETE eTraxis:StateAssignee a
            WHERE a.stateId = :state
            AND a.groupId IN (:ids)
        ');

        $query->execute([
            'state' => $state->getId(),
            'ids'   => $ids,
        ]);

        if ($command instanceof AddStateAssigneesCommand) {

            foreach ($groups as $group) {

                if ($group->getProjectId() === null || $group->getProjectId() == $projectId) {

                    $entity = new StateAssignee();

                    $entity
                        ->setStateId($state->getId())
                        ->setGroupId($group->getId())
                        ->setState($state)
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
