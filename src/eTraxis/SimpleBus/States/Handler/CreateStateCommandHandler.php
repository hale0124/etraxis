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
use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\States\CreateStateCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateStateCommandHandler
{
    protected $validator;
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $this->validator = $validator;
        $this->manager   = $manager;
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
        $template = $this->manager->find(Template::class, $command->template);

        if (!$template) {
            throw new NotFoundHttpException('Unknown template.');
        }

        $entity = new State($template, $command->type);

        $entity
            ->setName($command->name)
            ->setAbbreviation($command->abbreviation)
            ->setResponsible($command->type === StateType::FINAL ? StateResponsible::REMOVE : $command->responsible)
        ;

        if ($command->nextState) {

            /** @var State $nextState */
            $nextState = $this->manager->find(State::class, $command->nextState);

            if (!$nextState) {
                throw new NotFoundHttpException('Unknown next state.');
            }

            $entity->setNextState($nextState);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        if ($command->type === StateType::INITIAL) {

            $query = $this->manager->createQuery('
                UPDATE eTraxis:State s
                SET s.type = :interim
                WHERE s.template = :template AND s.type = :initial
            ');

            $query->execute([
                'template' => $template,
                'initial'  => StateType::INITIAL,
                'interim'  => StateType::INTERIM,
            ]);
        }

        $this->manager->persist($entity);
    }
}
