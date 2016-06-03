<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\CurrentUser;
use eTraxis\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Provider to load users from the database.
 */
class InternalUserProvider implements UserProviderInterface
{
    protected $logger;
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface        $logger
     * @param   EntityManagerInterface $manager
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $manager)
    {
        $this->logger  = $logger;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'provider' => AuthenticationProvider::ETRAXIS,
            'username' => $username,
        ]);

        if ($user) {
            $this->logger->info('eTraxis account is found.', [$username]);

            return new CurrentUser($user);
        }

        $user = $this->manager->getRepository(User::class)->findOneBy([
            'provider' => AuthenticationProvider::LDAP,
            'username' => $username,
        ]);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        $this->logger->info('LDAP account is found.', [$username]);

        $user->setPassword(null);

        return new CurrentUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof CurrentUser) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === CurrentUser::class;
    }
}
