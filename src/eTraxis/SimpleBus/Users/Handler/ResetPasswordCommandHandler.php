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
use eTraxis\SimpleBus\Users\ResetPasswordCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Command handler.
 */
class ResetPasswordCommandHandler
{
    protected $translator;
    protected $manager;
    protected $password_encoder;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface      $translator
     * @param   EntityManagerInterface   $manager
     * @param   PasswordEncoderInterface $password_encoder
     */
    public function __construct(
        TranslatorInterface      $translator,
        EntityManagerInterface   $manager,
        PasswordEncoderInterface $password_encoder)
    {
        $this->translator       = $translator;
        $this->manager          = $manager;
        $this->password_encoder = $password_encoder;
    }

    /**
     * Resets password for specified account.
     *
     * @param   ResetPasswordCommand $command
     *
     * @throws  BadRequestHttpException
     */
    public function handle(ResetPasswordCommand $command)
    {
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneBy(['resetToken' => $command->token])) {

            if ($user->isExternalAccount()) {
                throw new BadRequestHttpException($this->translator->trans('password.cant_change'));
            }

            if (!$user->isResetTokenExpired()) {

                try {
                    $encoded = $this->password_encoder->encodePassword($command->password, null);
                }
                catch (BadCredentialsException $e) {
                    throw new BadRequestHttpException($e->getMessage());
                }

                $user
                    ->setPassword($encoded)
                    ->clearResetToken()
                ;

                $this->manager->persist($user);
            }
        }
    }
}
