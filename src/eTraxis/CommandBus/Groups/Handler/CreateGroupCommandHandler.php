<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use eTraxis\CommandBus\CommandException;
use eTraxis\CommandBus\Groups\CreateGroupCommand;
use eTraxis\Entity\Group;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateGroupCommandHandler
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
     * Creates new group.
     *
     * @param   CreateGroupCommand $command
     *
     * @return  int ID of the created group.
     *
     * @throws  CommandException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateGroupCommand $command)
    {
        $entity = new Group();

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
        ;

        if ($command->project) {

            $repository = $this->doctrine->getRepository('eTraxis:Project');

            /** @var \eTraxis\Entity\Project $project */
            $project = $repository->find($command->project);

            if (!$project) {
                $this->logger->error('Unknown project.', [$command->project]);
                throw new NotFoundHttpException('Unknown project.');
            }

            $entity->setProject($project);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            $message = $this->translator->trans($errors->get(0)->getMessage());
            $this->logger->error($message);
            throw new CommandException($message);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();

        return $entity->getId();
    }
}
