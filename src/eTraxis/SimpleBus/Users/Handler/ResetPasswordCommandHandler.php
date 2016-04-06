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

use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\ResetPasswordCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
    protected $doctrine;
    protected $password_encoder;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface      $translator
     * @param   RegistryInterface        $doctrine
     * @param   PasswordEncoderInterface $password_encoder
     */
    public function __construct(
        TranslatorInterface      $translator,
        RegistryInterface        $doctrine,
        PasswordEncoderInterface $password_encoder)
    {
        $this->translator       = $translator;
        $this->doctrine         = $doctrine;
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
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneBy(['resetToken' => $command->token])) {

            if ($user->isLdap()) {
                throw new BadRequestHttpException($this->translator->trans('password.cant_change'));
            }

            if ($user->getResetTokenExpiresAt() > time()) {

                try {
                    $encoded = $this->password_encoder->encodePassword($command->password, null);
                }
                catch (BadCredentialsException $e) {
                    throw new BadRequestHttpException($e->getMessage());
                }

                $user
                    ->setPassword($encoded)
                    ->setPasswordSetAt(time())
                    ->setResetToken(null)
                ;

                $this->doctrine->getManager()->persist($user);
                $this->doctrine->getManager()->flush();
            }
        }
    }
}
