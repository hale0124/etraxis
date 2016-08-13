<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Users\UpdateUserCommand;
use eTraxis\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateUserCommandHandler
{
    protected $validator;
    protected $manager;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     * @param   TokenStorageInterface  $token_storage
     */
    public function __construct(
        ValidatorInterface     $validator,
        EntityManagerInterface $manager,
        TokenStorageInterface  $token_storage)
    {
        $this->validator     = $validator;
        $this->manager       = $manager;
        $this->token_storage = $token_storage;
    }

    /**
     * Updates specified account.
     *
     * @param   UpdateUserCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateUserCommand $command)
    {
        /** @var \eTraxis\Security\CurrentUser $user */
        $user = $this->token_storage->getToken()->getUser();

        /** @var User $entity */
        $entity = $this->manager->find(User::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown user.');
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
        if ($entity->getId() !== $user->getId()) {
            $entity
                ->setAdmin($command->admin)
                ->setDisabled($command->disabled)
            ;
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
