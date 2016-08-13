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
use eTraxis\CommandBus\States\SetInitialStateCommand;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetInitialStateCommandHandler
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
     * Makes specified state initial for its template.
     *
     * @param   SetInitialStateCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetInitialStateCommand $command)
    {
        /** @var State $entity */
        $entity = $this->manager->find(State::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown state.');
        }

        $query = $this->manager->createQuery('
            UPDATE eTraxis:State s
            SET s.type = :interim
            WHERE s.template = :template AND s.type = :initial
        ');

        $query->execute([
            'template' => $entity->getTemplate(),
            'initial'  => StateType::IS_INITIAL,
            'interim'  => StateType::IS_INTERIM,
        ]);

        $query = $this->manager->createQuery('
            UPDATE eTraxis:State s
            SET s.type = :initial
            WHERE s.id = :id
        ');

        $query->execute([
            'id'      => $command->id,
            'initial' => StateType::IS_INITIAL,
        ]);
    }
}
