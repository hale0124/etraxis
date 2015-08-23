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

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\CommandException;
use eTraxis\CommandBus\Users\ResetPasswordCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Command handler.
 */
class ResetPasswordCommandHandler
{
    protected $logger;
    protected $doctrine;
    protected $password_encoder;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface          $logger
     * @param   RegistryInterface        $doctrine
     * @param   PasswordEncoderInterface $password_encoder
     */
    public function __construct(
        LoggerInterface          $logger,
        RegistryInterface        $doctrine,
        PasswordEncoderInterface $password_encoder)
    {
        $this->logger           = $logger;
        $this->doctrine         = $doctrine;
        $this->password_encoder = $password_encoder;
    }

    /**
     * Resets password for specified account.
     *
     * @param   ResetPasswordCommand $command
     *
     * @throws  CommandException
     */
    public function handle(ResetPasswordCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $user */
        if ($user = $repository->findOneBy(['resetToken' => $command->token])) {

            if ($user->getResetTokenExpiresAt() > time()) {

                try {
                    $encoded = $this->password_encoder->encodePassword($command->password, null);
                }
                catch (BadCredentialsException $e) {
                    $this->logger->error($e->getMessage());
                    throw new CommandException($e->getMessage());
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
