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
use eTraxis\Service\Mailer\MailerInterface;
use eTraxis\SimpleBus\Users\ForgotPasswordCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class ForgotPasswordCommandHandler
{
    protected $doctrine;
    protected $mailer;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     * @param   MailerInterface   $mailer
     */
    public function __construct(RegistryInterface $doctrine, MailerInterface $mailer)
    {
        $this->doctrine = $doctrine;
        $this->mailer   = $mailer;
    }

    /**
     * Marks password of specified eTraxis account as forgotten.
     *
     * @param   ForgotPasswordCommand $command
     */
    public function handle(ForgotPasswordCommand $command)
    {
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $user */
        if ($user = $repository->findOneBy(['username' => $command->username . '@eTraxis'])) {

            $token = $user->generateResetToken();

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();

            $this->mailer->send(
                [$user->getEmail() => $user->getFullname()],
                'Reset password link for your eTraxis account',
                'email/forgot_password.html.twig',
                [
                    'token' => $token,
                    'ip'    => $command->ip,
                ]
            );
        }
    }
}
