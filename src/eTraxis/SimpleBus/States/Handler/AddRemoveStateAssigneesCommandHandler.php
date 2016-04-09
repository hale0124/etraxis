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
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class AddRemoveStateAssigneesCommandHandler
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
            throw new NotFoundHttpException('Unknown state.');
        }

        $project = $state->getTemplate()->getProject();

        /** @var Group[] $groups */
        $groups = $this->doctrine->getRepository(Group::class)->findBy([
            'id' => $command->groups,
        ]);

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->beginTransaction();

        $query = $em->createQuery('
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

                    $em->persist($entity);
                }
            }
        }

        $em->flush();
        $em->commit();
    }
}
