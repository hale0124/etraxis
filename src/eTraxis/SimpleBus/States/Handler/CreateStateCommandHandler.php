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
use eTraxis\SimpleBus\States\CreateStateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateStateCommandHandler
{
    protected $logger;
    protected $validator;
    protected $translator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface     $logger
     * @param   ValidatorInterface  $validator
     * @param   TranslatorInterface $translator
     * @param   RegistryInterface   $doctrine
     */
    public function __construct(
        LoggerInterface     $logger,
        ValidatorInterface  $validator,
        TranslatorInterface $translator,
        RegistryInterface   $doctrine)
    {
        $this->logger     = $logger;
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
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
        /** @var \eTraxis\Entity\Template $template */
        $template = $this->doctrine->getRepository('eTraxis:Template')->find($command->template);

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
            ->setResponsible($command->responsible)
        ;

        if ($command->nextState) {

            /** @var \eTraxis\Entity\State $nextState */
            $nextState = $this->doctrine->getRepository('eTraxis:State')->find($command->nextState);

            if (!$nextState) {
                $this->logger->error('Unknown next state.', [$command->nextState]);
                throw new NotFoundHttpException('Unknown next state.');
            }

            $entity->setNextState($nextState);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            $message = $this->translator->trans($errors->get(0)->getMessage());
            $this->logger->error($message);
            throw new BadRequestHttpException($message);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
