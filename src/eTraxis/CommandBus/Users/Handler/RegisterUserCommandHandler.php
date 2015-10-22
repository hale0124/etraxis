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

use eTraxis\CommandBus\Users\RegisterUserCommand;
use eTraxis\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
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
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     * @param   string            $locale
     * @param   string            $theme
     */
    public function __construct(LoggerInterface $logger, RegistryInterface $doctrine, $locale, $theme)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
        $this->locale   = $locale;
        $this->theme    = $theme;
    }

    /**
     * Registers LDAP account in eTraxis database.
     * If specified account is already registered - its cached display name and email address are being refreshed.
     *
     * @param   RegisterUserCommand $command
     *
     * @return  int ID of the registered user.
     */
    public function handle(RegisterUserCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var User $user */
        $user = $repository->findOneBy([
            'username' => $command->username,
            'isLdap'   => 1,
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

        return $user->getId();
    }
}
