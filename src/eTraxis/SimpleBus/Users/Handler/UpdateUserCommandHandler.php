<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\CommandException;
use eTraxis\SimpleBus\Users\UpdateUserCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Updates specified account.
 */
class UpdateUserCommandHandler
{
    protected $logger;
    protected $validator;
    protected $translator;
    protected $doctrine;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface       $logger
     * @param   ValidatorInterface    $validator
     * @param   TranslatorInterface   $translator
     * @param   RegistryInterface     $doctrine
     * @param   TokenStorageInterface $token_storage
     */
    public function __construct(
        LoggerInterface       $logger,
        ValidatorInterface    $validator,
        TranslatorInterface   $translator,
        RegistryInterface     $doctrine,
        TokenStorageInterface $token_storage)
    {
        $this->logger        = $logger;
        $this->validator     = $validator;
        $this->translator    = $translator;
        $this->doctrine      = $doctrine;
        $this->token_storage = $token_storage;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateUserCommand $command)
    {
        /** @var \eTraxis\Entity\User $user */
        $user = $this->token_storage->getToken()->getUser();

        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $entity
            ->setUsername($command->username)
            ->setFullname($command->fullname)
            ->setEmail($command->email)
            ->setDescription($command->description)
            ->setLocale($command->locale)
            ->setTheme($command->theme)
            ->setTimezone($command->timezone)
        ;

        // Don't disable yourself.
        if ($entity->getId() != $user->getId()) {
            $entity
                ->setAdmin($command->admin)
                ->setDisabled($command->disabled)
            ;
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            $message = $this->translator->trans($errors->get(0)->getMessage());
            $this->logger->error($message);
            throw new CommandException($message);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
