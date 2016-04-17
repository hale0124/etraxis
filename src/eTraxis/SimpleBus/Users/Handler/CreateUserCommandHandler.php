<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\CreateUserCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateUserCommandHandler
{
    protected $validator;
    protected $manager;
    protected $password_encoder;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface       $validator
     * @param   EntityManagerInterface   $manager
     * @param   PasswordEncoderInterface $password_encoder
     */
    public function __construct(
        ValidatorInterface       $validator,
        EntityManagerInterface   $manager,
        PasswordEncoderInterface $password_encoder)
    {
        $this->validator        = $validator;
        $this->manager          = $manager;
        $this->password_encoder = $password_encoder;
    }

    /**
     * Creates new account.
     *
     * @param   CreateUserCommand $command
     *
     * @throws  BadRequestHttpException
     */
    public function handle(CreateUserCommand $command)
    {
        try {
            $encoded = $this->password_encoder->encodePassword($command->password, null);
        }
        catch (BadCredentialsException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $entity = new User();

        $entity
            ->setUsername($command->username)
            ->setFullname($command->fullname)
            ->setEmail($command->email)
            ->setDescription($command->description)
            ->setPassword($encoded)
            ->setAdmin($command->admin)
            ->setDisabled($command->disabled)
            ->setLdap(false)
            ->setLocale($command->locale)
            ->setTheme($command->theme)
            ->setTimezone($command->timezone)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
