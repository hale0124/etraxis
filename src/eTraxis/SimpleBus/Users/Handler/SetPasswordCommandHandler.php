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
use eTraxis\SimpleBus\Users\SetPasswordCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Sets password for specified account.
 */
class SetPasswordCommandHandler
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
     * {@inheritdoc}
     */
    public function handle(SetPasswordCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $entity */
        $entity = $repository->find($command->id);

        if ($entity) {

            try {
                $encoded = $this->password_encoder->encodePassword($command->password, null);
            }
            catch (BadCredentialsException $e) {
                $this->logger->error($e->getMessage());
                throw new CommandException($e->getMessage());
            }

            $entity
                ->setPassword($encoded)
                ->setPasswordSetAt(time())
            ;

            $this->doctrine->getManager()->persist($entity);
            $this->doctrine->getManager()->flush();
        }
    }
}
