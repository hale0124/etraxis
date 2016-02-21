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
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\States\CreateStateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateStateCommandHandler
{
    protected $logger;
    protected $validator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface    $logger
     * @param   ValidatorInterface $validator
     * @param   RegistryInterface  $doctrine
     */
    public function __construct(
        LoggerInterface    $logger,
        ValidatorInterface $validator,
        RegistryInterface  $doctrine)
    {
        $this->logger    = $logger;
        $this->validator = $validator;
        $this->doctrine  = $doctrine;
    }

    /**
     * Creates new state.
     *
     * @param   CreateStateCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateStateCommand $command)
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->find($command->template);

        if (!$template) {
            $this->logger->error('Unknown template.', [$command->template]);
            throw new NotFoundHttpException('Unknown template.');
        }

        $entity = new State();

        $entity
            ->setTemplate($template)
            ->setName($command->name)
            ->setAbbreviation($command->abbreviation)
            ->setType($command->type)
            ->setResponsible($command->type == State::TYPE_FINAL ? State::RESPONSIBLE_REMOVE : $command->responsible)
        ;

        if ($command->nextState) {

            /** @var State $nextState */
            $nextState = $this->doctrine->getRepository(State::class)->find($command->nextState);

            if (!$nextState) {
                $this->logger->error('Unknown next state.', [$command->nextState]);
                throw new NotFoundHttpException('Unknown next state.');
            }

            $entity->setNextState($nextState);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            $message = $errors->get(0)->getMessage();
            $this->logger->error($message);
            throw new BadRequestHttpException($message);
        }

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->beginTransaction();

        if ($command->type == State::TYPE_INITIAL) {

            $query = $em->createQuery('
                UPDATE eTraxis:State s
                SET s.type = :interim
                WHERE s.templateId = :id AND s.type = :initial
            ');

            $query->execute([
                'id'      => $command->template,
                'initial' => State::TYPE_INITIAL,
                'interim' => State::TYPE_INTERIM,
            ]);
        }

        $em->persist($entity);
        $em->flush();
        $em->commit();
    }
}
