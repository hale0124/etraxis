<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Current authenticated user.
 */
class CurrentUser implements AdvancedUserInterface
{
    // Roles.
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER  = 'ROLE_USER';

    // Data from corresponding "User" entity.
    private $password;
    private $isLocked;
    private $data;

    /**
     * Constructor.
     *
     * @param   User $user
     */
    public function __construct(User $user)
    {
        $this->password = $user->getPassword();
        $this->isLocked = $user->isLocked();
        $this->data     = $user->jsonSerialize();
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getFullname()
    {
        return $this->data['fullname'];
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isLdap()
    {
        return $this->data['isLdap'];
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getLocale()
    {
        return $this->data['locale'];
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getTheme()
    {
        return $this->data['theme'];
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getTimezone()
    {
        return $this->data['timezone'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [self::ROLE_USER];

        if ($this->data['isAdmin']) {
            $roles[] = self::ROLE_ADMIN;
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
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
        return $this->data['username'];
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->password = null;
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
        return !$this->isLocked;
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
        return !$this->data['isDisabled'];
    }
}
