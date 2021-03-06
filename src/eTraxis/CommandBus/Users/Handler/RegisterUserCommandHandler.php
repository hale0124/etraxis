<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Users\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Users\RegisterUserCommand;
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * Command handler.
 */
class RegisterUserCommandHandler
{
    protected $logger;
    protected $manager;
    protected $locale;
    protected $theme;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface        $logger
     * @param   EntityManagerInterface $manager
     * @param   string                 $locale
     * @param   string                 $theme
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager, string $locale, string $theme)
    {
        $this->logger  = $logger;
        $this->manager = $manager;
        $this->locale  = $locale;
        $this->theme   = $theme;
    }

    /**
     * Registers LDAP account in eTraxis database.
     * If specified account is already registered - its cached display name and email address are being refreshed.
     *
     * @param   RegisterUserCommand $command
     */
    public function handle(RegisterUserCommand $command)
    {
        $repository = $this->manager->getRepository(User::class);

        /** @var User $user */
        $user = $repository->findOneBy([
            'provider' => AuthenticationProvider::LDAP,
            'username' => $command->username,
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

            $user = new User(AuthenticationProvider::LDAP);

            $user
                ->setUsername($command->username)
                ->setFullname($command->fullname)
                ->setEmail($command->email)
                ->setAdmin(false)
                ->setDisabled(false)
                ->setLocale($this->locale)
                ->setTheme($this->theme)
            ;
        }

        $this->manager->persist($user);
    }
}
