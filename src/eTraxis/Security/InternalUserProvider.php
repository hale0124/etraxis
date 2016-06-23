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
use eTraxis\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Provider to load users from the database.
 */
class InternalUserProvider implements UserProviderInterface
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
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

        if (!$user) {
            throw new UsernameNotFoundException();
        }

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

        /** @var User $user */
        $user = $this->manager->getRepository(User::class)->findOneBy([
            'username' => $user->getUsername(),
        ]);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        return new CurrentUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === CurrentUser::class;
    }
}
