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

namespace eTraxis\SimpleBus\CommandHandler\User;

use eTraxis\Model\User;
use eTraxis\SimpleBus\Command\User\RegisterUserCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Registers LDAP account in eTraxis database.
 * If specified account is already registered - its cached display name and email address are being refreshed.
 */
class RegisterUserCommandHandler
{
    protected $logger;
    protected $doctrine;
    protected $locale;
    protected $theme;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger   Debug logger.
     * @param   RegistryInterface $doctrine Doctrine entity managers registry.
     * @param   string            $locale   Default locale.
     * @param   string            $theme    Default theme.
     */
    public function __construct(
        LoggerInterface $logger,
        RegistryInterface $doctrine,
        $locale,
        $theme)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
        $this->locale   = $locale;
        $this->theme    = $theme;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(RegisterUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var User $user */
        $user = $repository->findOneBy([
            'username' => $command->username,
            'isLdap'   => true,
        ]);

        // The account already exists - update display name and email.
        if ($user) {

            $this->logger->info('Update LDAP account.', [$command->username, $command->fullname, $command->email]);

            $user
                ->setFullname($command->fullname)
                ->setEmail($command->email)
            ;
        }
        // Register new account.
        else {

            $this->logger->info('Register LDAP account.', [$command->username, $command->fullname, $command->email]);

            $user = new User();

            $user
                ->setUsername($command->username)
                ->setFullname($command->fullname)
                ->setEmail($command->email)
                ->setPassword(null)
                ->setAdmin(false)
                ->setDisabled(false)
                ->setLdap(true)
                ->setLocale($this->locale)
                ->setTheme($this->theme)
            ;
        }

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();

        $command->id = $user->getId();
    }
}
