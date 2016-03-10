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

use eTraxis\Entity\User;
use eTraxis\Repository\UsersRepository;
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
    protected $users;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface  $logger
     * @param   UsersRepository  $users
     */
    public function __construct(LoggerInterface $logger, UsersRepository $users)
    {
        $this->logger = $logger;
        $this->users  = $users;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->users->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => 0,
        ]);

        if ($user) {
            $this->logger->info('eTraxis account is found.', [$username]);

            return $user;
        }

        $user = $this->users->findOneBy([
            'username' => $username,
            'isLdap'   => 1,
        ]);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        $this->logger->info('LDAP account is found.', [$username]);

        $user->setPassword(null);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
