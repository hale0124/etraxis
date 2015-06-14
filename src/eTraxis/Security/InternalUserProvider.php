<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace eTraxis\Security;

use eTraxis\Model\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Provider to load users from the database.
 */
class InternalUserProvider implements UserProviderInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface   $logger
     * @param   RegistryInterface $doctrine
     */
    public function __construct(LoggerInterface $logger, RegistryInterface $doctrine)
    {
        $this->logger   = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var User $user */
        $user = $repository->findOneBy([
            'username' => $username . '@eTraxis',
            'isLdap'   => false,
        ]);

        if ($user) {
            $this->logger->info('eTraxis account is found.', [$username]);

            return $user;
        }

        $user = $repository->findOneBy([
            'username' => $username,
            'isLdap'   => true,
        ]);

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        $this->logger->info('LDAP account is found.', [$username]);

        $user->setPassword(null);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'eTraxis\Model\User';
    }
}
