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
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Current authenticated user.
 */
class CurrentUser implements AdvancedUserInterface
{
    // Roles.
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER  = 'ROLE_USER';

    // Serialized "User" entity (JSON).
    private $user;

    /**
     * Constructor.
     *
     * @param   User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user->jsonSerialize();

        $this->user['isExternalAccount'] = $user->isExternalAccount();
        $this->user['isLocked']          = $user->isLocked();
        $this->user['password']          = $user->getPassword();
    }

    /**
     * Proxy getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->user['id'];
    }

    /**
     * Proxy getter.
     *
     * @return  bool
     */
    public function isExternalAccount()
    {
        return $this->user['isExternalAccount'];
    }

    /**
     * Proxy getter.
     *
     * @return  string
     */
    public function getFullname()
    {
        return $this->user['fullname'];
    }

    /**
     * Proxy getter.
     *
     * @return  string
     */
    public function getLocale()
    {
        return $this->user['locale'];
    }

    /**
     * Proxy getter.
     *
     * @return  string
     */
    public function getTheme()
    {
        return $this->user['theme'];
    }

    /**
     * Proxy getter.
     *
     * @return  string
     */
    public function getTimezone()
    {
        return $this->user['timezone'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [self::ROLE_USER];

        if ($this->user['isAdmin']) {
            $roles[] = self::ROLE_ADMIN;
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->user['password'];
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->user['username'];
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->user['password'] = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return !$this->user['isLocked'];
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return !$this->user['isDisabled'];
    }
}
