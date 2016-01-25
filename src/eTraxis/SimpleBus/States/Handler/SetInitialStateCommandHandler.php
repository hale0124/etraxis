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

use eTraxis\Entity\State;
use eTraxis\SimpleBus\States\SetInitialStateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class SetInitialStateCommandHandler
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
     * Makes specified state initial for its template.
     *
     * @param   SetInitialStateCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SetInitialStateCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:State');

        /** @var \eTraxis\Entity\State $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown state.', [$command->id]);
            throw new NotFoundHttpException('Unknown state.');
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->beginTransaction();

        $query = $em->createQuery('
            UPDATE eTraxis:State s
            SET s.type = :interim
            WHERE s.templateId = :id AND s.type = :initial
        ');

        $query->execute([
            'id'      => $entity->getTemplateId(),
            'initial' => State::TYPE_INITIAL,
            'interim' => State::TYPE_INTERIM,
        ]);

        $query = $em->createQuery('
            UPDATE eTraxis:State s
            SET s.type = :initial
            WHERE s.id = :id
        ');

        $query->execute([
            'id'      => $command->id,
            'initial' => State::TYPE_INITIAL,
        ]);

        $em->commit();
    }
}
