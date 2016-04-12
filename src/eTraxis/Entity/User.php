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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use eTraxis\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * User.
 *
 * @ORM\Table(name="tbl_accounts",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_accounts", columns={"username"})
 *            })
 * @ORM\Entity(repositoryClass="eTraxis\Repository\UsersRepository")
 * @Assert\UniqueEntity(fields={"username"}, message="user.conflict.username")
 */
class User extends AbstractUser
{
    // Roles.
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER  = 'ROLE_USER';

    // Authentication source.
    const AUTH_INTERNAL = 'eTraxis';
    const AUTH_LDAP     = 'LDAP';

    // Constraints.
    const MAX_USERNAME    = 100;
    const MAX_FULLNAME    = 64;
    const MAX_EMAIL       = 50;
    const MAX_DESCRIPTION = 100;

    // Actions.
    const SET_EXPIRED_PASSWORD = 'user.set_expired_password';
    const DELETE               = 'user.delete';
    const DISABLE              = 'user.disable';
    const ENABLE               = 'user.enable';
    const UNLOCK               = 'user.unlock';

    /**
     * @var int Unique ID.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="account_id", type="integer")
     */
    private $id;

    /**
     * @var string User's login.
     *
     * @ORM\Column(name="username", type="string", length=112)
     */
    private $username;

    /**
     * @var string User's full name.
     *
     * @ORM\Column(name="fullname", type="string", length=64)
     */
    private $fullname;

    /**
     * @var string Email address.
     *
     * @ORM\Column(name="email", type="string", length=50)
     */
    private $email;

    /**
     * @var string Description of the user.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var string Password hash.
     *
     * @ORM\Column(name="passwd", type="string", length=32, nullable=true)
     */
    private $password;

    /**
     * @var int Unix Epoch timestamp when the password was changed last time.
     *
     * @ORM\Column(name="passwd_expire", type="integer")
     */
    private $passwordSetAt;

    /**
     * @var string Hash for password reset.
     *
     * @ORM\Column(name="auth_token", type="string", length=32, nullable=true)
     */
    private $resetToken;

    /**
     * @var int Unix Epoch timestamp when the password reset token expires.
     *
     * @ORM\Column(name="token_expire", type="integer")
     */
    private $resetTokenExpiresAt;

    /**
     * @var int Number of consecutive unsuccessful attempts to authenticate.
     *
     * @ORM\Column(name="locks_count", type="integer")
     */
    private $authAttempts;

    /**
     * @var int Unix Epoch timestamp which the account is locked till.
     *          If in the past, the account is considered as not locked.
     *
     * @ORM\Column(name="lock_time", type="integer")
     */
    private $lockedUntil;

    /**
     * @var int Whether user has administration privileges.
     *
     * @ORM\Column(name="is_admin", type="integer")
     */
    private $isAdmin;

    /**
     * @var int Whether user is disabled by administrator.
     *
     * @ORM\Column(name="is_disabled", type="integer")
     */
    private $isDisabled;

    /**
     * @var int Whether account is internal (FALSE), or from LDAP server (TRUE).
     *
     * @ORM\Column(name="is_ldapuser", type="integer")
     */
    private $isLdap;

    /**
     * @var int Locale ID of user interface.
     *
     * @ORM\Column(name="locale", type="integer")
     */
    private $locale;

    /**
     * @var string Name of UI theme (e.g. "Emerald").
     *
     * @ORM\Column(name="theme_name", type="string", length=50)
     */
    private $theme;

    /**
     * @var int Timezone ID.
     *
     * @ORM\Column(name="timezone", type="integer")
     */
    private $timezone;

    /**
     * @var View Current view.
     *
     * @ORM\OneToOne(targetEntity="View")
     * @ORM\JoinColumn(name="view_id", referencedColumnName="view_id")
     */
    public $view;

    /**
     * @var ArrayCollection List of groups the user is member of.
     *
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="members")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $groups;

    /**
     * @var UserDeprecated Deprecated features.
     *
     * @ORM\Embedded(class="UserDeprecated", columnPrefix=false)
     */
    private $deprecated;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->password            = null;
        $this->passwordSetAt       = 0;
        $this->resetToken          = null;
        $this->resetTokenExpiresAt = 0;
        $this->authAttempts        = 0;
        $this->lockedUntil         = 0;

        $this->isAdmin    = 0;
        $this->isDisabled = 0;
        $this->isLdap     = 0;

        $this->timezone = 0;

        $this->groups     = new ArrayCollection();
        $this->deprecated = new UserDeprecated();
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Property setter.
     *
     * @param   string $username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        if (!$this->isLdap) {
            $this->username .= '@eTraxis';
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getUsername()
    {
        return str_replace('@eTraxis', null, $this->username);
    }

    /**
     * Property setter.
     *
     * @param   string $fullname
     *
     * @return  self
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Property setter.
     *
     * @param   string $email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Property setter.
     *
     * @param   string $description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Property setter.
     *
     * @param   string $password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        if (!$this->isLdap) {
            $this->password      = $password;
            $this->passwordSetAt = time();
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Checks whether user's password is expired.
     *
     * @param   int $days Number of days a password is valid for.
     *
     * @return  bool
     */
    public function isPasswordExpired($days)
    {
        $expires = $this->passwordSetAt + $days * 86400;

        return $expires <= time();
    }

    /**
     * Generates new "password reset" token.
     *
     * @return  string Generated token.
     */
    public function generateResetToken()
    {
        $this->resetToken          = Uuid::uuid4()->getHex();
        $this->resetTokenExpiresAt = time() + 7200; // 2 hours expiration

        return $this->resetToken;
    }

    /**
     * Clears current "password reset" token.
     *
     * @return  self
     */
    public function clearResetToken()
    {
        $this->resetToken          = null;
        $this->resetTokenExpiresAt = 0;

        return $this;
    }

    /**
     * Checks whether current "password reset" token is expired.
     *
     * @return  bool
     */
    public function isResetTokenExpired()
    {
        return $this->resetTokenExpiresAt <= time();
    }

    /**
     * Increases locks count for the account.
     *
     * @param   int $max_auth_attempts Maximum number of attempts to log in.
     * @param   int $lock_time         Number of minutes to lock out for.
     *
     * @return  bool Whether the account became locked.
     */
    public function lock($max_auth_attempts, $lock_time)
    {
        if (!$this->isLdap) {

            $this->authAttempts++;

            if ($this->authAttempts >= $max_auth_attempts) {
                $this->authAttempts = 0;
                $this->lockedUntil  = time() + $lock_time * 60;

                return true;
            }
        }

        return false;
    }

    /**
     * Unlocks the account.
     */
    public function unlock()
    {
        $this->authAttempts = 0;
        $this->lockedUntil  = 0;
    }

    /**
     * Checks whether account is locked.
     *
     * @return  bool
     */
    public function isAccountNonLocked()
    {
        return $this->lockedUntil < time();
    }

    /**
     * Property setter.
     *
     * @param   bool $isAdmin
     *
     * @return  self
     */
    public function setAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isAdmin()
    {
        return (bool) $this->isAdmin;
    }

    /**
     * Property setter.
     *
     * @param   bool $isDisabled
     *
     * @return  self
     */
    public function setDisabled($isDisabled)
    {
        $this->isDisabled = $isDisabled ? 1 : 0;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isDisabled()
    {
        return (bool) $this->isDisabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return !$this->isDisabled;
    }

    /**
     * Property setter.
     *
     * @param   bool $isLdap
     *
     * @return  self
     */
    public function setLdap($isLdap)
    {
        $this->isLdap = $isLdap ? 1 : 0;

        $this->username = str_replace('@eTraxis', null, $this->username);

        if ($isLdap) {
            $this->password      = null;
            $this->passwordSetAt = 0;
        }
        else {
            $this->username .= '@eTraxis';
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isLdap()
    {
        return (bool) $this->isLdap;
    }

    /**
     * Returns authentication source of the user.
     *
     * @return  string
     */
    public function getAuthenticationSource()
    {
        return $this->isLdap ? self::AUTH_LDAP : self::AUTH_INTERNAL;
    }

    /**
     * Property setter.
     *
     * @param   string $locale
     *
     * @return  self
     */
    public function setLocale($locale)
    {
        $locales = array_flip(Collection\LegacyLocale::getCollection());

        if (array_key_exists($locale, $locales)) {
            $this->locale = $locales[$locale];
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getLocale()
    {
        $locales = Collection\LegacyLocale::getCollection();

        if (!array_key_exists($this->locale, $locales)) {
            $this->locale = 1000;
        }

        return $locales[$this->locale];
    }

    /**
     * Property setter.
     *
     * @param   string $theme
     *
     * @return  self
     */
    public function setTheme($theme)
    {
        if (in_array($theme, Collection\Theme::getAllKeys())) {
            $this->theme = $theme;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getTheme()
    {
        $theme = strtolower($this->theme);

        if (!in_array($theme, Collection\Theme::getAllKeys())) {
            $theme = 'azure';
        }

        return $theme;
    }

    /**
     * Property setter.
     *
     * @param   int $timezone
     *
     * @return  self
     */
    public function setTimezone($timezone)
    {
        if (in_array($timezone, Collection\Timezone::getAllKeys())) {
            $this->timezone = $timezone;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getTimezone()
    {
        if (!in_array($this->timezone, Collection\Timezone::getAllKeys())) {
            $this->timezone = 0;
        }

        return $this->timezone;
    }

    /**
     * Get list of groups the user is member of.
     *
     * @return  Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = [self::ROLE_USER];

        if ($this->isAdmin) {
            $roles[] = self::ROLE_ADMIN;
        }

        return $roles;
    }
}
