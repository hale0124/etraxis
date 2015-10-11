<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\Users\ForgotPasswordCommand;
use eTraxis\Service\MailerInterface;
use Rhumsaa\Uuid\Uuid;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class ForgotPasswordCommandHandler
{
    protected $doctrine;
    protected $mailer;
    protected $sender_address;
    protected $sender_name;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     * @param   MailerInterface   $mailer
     * @param   string            $sender_address
     * @param   string            $sender_name
     */
    public function __construct(RegistryInterface $doctrine, MailerInterface $mailer, $sender_address, $sender_name)
    {
        $this->doctrine       = $doctrine;
        $this->mailer         = $mailer;
        $this->sender_address = $sender_address;
        $this->sender_name    = $sender_name;
    }

    /**
     * Marks password of specified eTraxis account as forgotten.
     *
     * @param   ForgotPasswordCommand $command
     */
    public function handle(ForgotPasswordCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        if ($user = $repository->findOneBy(['username' => $command->username . '@eTraxis'])) {

            $user
                ->setResetToken(Uuid::uuid4()->getHex())
                ->setResetTokenExpiresAt(time() + 7200)
            ;

            $this->doctrine->getManager()->persist($user);
            $this->doctrine->getManager()->flush();

            $this->mailer->send(
                $this->sender_address,
                $this->sender_name,
                [$user->getEmail() => $user->getFullName()],
                'Reset password link for your eTraxis account',
                'email/forgot_password.html.twig',
                [
                    'token' => $user->getResetToken(),
                    'ip'    => $command->ip,
                ]
            );
        }
    }
}
