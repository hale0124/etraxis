<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\SimpleBus\Users\ResetPasswordCommand;
use Psr\Log\LoggerInterface;
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
    protected $logger;
    protected $translator;
    protected $doctrine;
    protected $password_encoder;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface          $logger
     * @param   TranslatorInterface      $translator
     * @param   RegistryInterface        $doctrine
     * @param   PasswordEncoderInterface $password_encoder
     */
    public function __construct(
        LoggerInterface          $logger,
        TranslatorInterface      $translator,
        RegistryInterface        $doctrine,
        PasswordEncoderInterface $password_encoder)
    {
        $this->logger           = $logger;
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
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        if ($user = $repository->findOneBy(['resetToken' => $command->token])) {

            if ($user->isLdap()) {
                $message = $this->translator->trans('password.cant_change');
                $this->logger->error($message);
                throw new BadRequestHttpException($message);
            }

            if ($user->getResetTokenExpiresAt() > time()) {

                try {
                    $encoded = $this->password_encoder->encodePassword($command->password, null);
                }
                catch (BadCredentialsException $e) {
                    $this->logger->error($e->getMessage());
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
