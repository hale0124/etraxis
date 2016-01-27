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
use eTraxis\Collection\Theme;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

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
class User implements AdvancedUserInterface
{
    // Roles.
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER  = 'ROLE_USER';

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
     * @ORM\Column(name="account_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @var int Locale ID of user interface.
     *
     * @ORM\Column(name="locale", type="integer")
     */
    private $locale;

    /**
     * @var int Timezone ID.
     *
     * @ORM\Column(name="timezone", type="integer")
     */
    private $timezone;

    /**
     * @deprecated 4.1.0 Number of rows in text boxes.
     * @ORM\Column(name="text_rows", type="integer")
     */
    private $textRows;

    /**
     * @deprecated 4.1.0 Number of rows per page in the list.
     * @ORM\Column(name="page_rows", type="integer")
     */
    private $pageRows;

    /**
     * @deprecated 4.1.0 Number of bookmarks per page in the list.
     * @ORM\Column(name="page_bkms", type="integer")
     */
    private $pageBkms;

    /**
     * @deprecated 4.1.0 How often eTraxis pages should be auto-reloaded (in minutes). Zero disables auto-refresh.
     * @ORM\Column(name="auto_refresh", type="integer")
     */
    private $autoRefresh;

    /**
     * @deprecated 4.1.0 ASCII code of character that should be used as CSV delimiter when user exports list of records to CSV file.
     * @ORM\Column(name="csv_delim", type="integer")
     */
    private $csvDelim;

    /**
     * @deprecated 4.1.0 Characters set that should be used when user exports list of records to CSV file.
     * @ORM\Column(name="csv_encoding", type="integer")
     */
    private $csvEncoding;

    /**
     * @deprecated 4.1.0 Line endings that should be used when user exports list of records to CSV file.
     * @ORM\Column(name="csv_line_ends", type="integer")
     */
    private $csvLineEnds;

    /**
     * @var int Current view ID.
     *
     * @ORM\Column(name="view_id", type="integer", nullable=true)
     */
    private $viewId;

    /**
     * @var string Name of UI theme (e.g. "Emerald").
     *
     * @ORM\Column(name="theme_name", type="string", length=50)
     */
    private $theme;

    /**
     * @var ArrayCollection List of groups the user is member of.
     *
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="users")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->passwordSetAt       = 0;
        $this->resetToken          = null;
        $this->resetTokenExpiresAt = 0;

        $this->isAdmin    = 0;
        $this->isDisabled = 0;
        $this->isLdap     = 0;

        $this->authAttempts = 0;
        $this->lockedUntil  = 0;

        $this->timezone = 0;

        $this->textRows    = 0;
        $this->pageRows    = 0;
        $this->pageBkms    = 0;
        $this->autoRefresh = 0;
        $this->csvDelim    = 0;
        $this->csvEncoding = 0;
        $this->csvLineEnds = 0;

        $this->groups = new ArrayCollection();
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  string
     */
    public function getUsername()
    {
        return str_replace('@eTraxis', null, $this->username);
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Standard setter.
     *
     * @param   string $password
     *
     * @return  self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Standard setter.
     *
     * @param   int $passwordSetAt
     *
     * @return  self
     */
    public function setPasswordSetAt($passwordSetAt)
    {
        $this->passwordSetAt = $passwordSetAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getPasswordSetAt()
    {
        return $this->passwordSetAt;
    }

    /**
     * Standard setter.
     *
     * @param   string $resetToken
     *
     * @return  self
     */
    public function setResetToken($resetToken)
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getResetToken()
    {
        return $this->resetToken;
    }

    /**
     * Standard setter.
     *
     * @param   int $resetTokenExpiresAt
     *
     * @return  self
     */
    public function setResetTokenExpiresAt($resetTokenExpiresAt)
    {
        $this->resetTokenExpiresAt = $resetTokenExpiresAt;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getResetTokenExpiresAt()
    {
        return $this->resetTokenExpiresAt;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  bool
     */
    public function isAdmin()
    {
        return (bool) $this->isAdmin;
    }

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  bool
     */
    public function isDisabled()
    {
        return (bool) $this->isDisabled;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isLdap
     *
     * @return  self
     */
    public function setLdap($isLdap)
    {
        $this->isLdap = $isLdap ? 1 : 0;

        $this->username = str_replace('@eTraxis', null, $this->username);

        if (!$isLdap) {
            $this->username .= '@eTraxis';
        }

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isLdap()
    {
        return (bool) $this->isLdap;
    }

    /**
     * Standard setter.
     *
     * @param   int $authAttempts
     *
     * @return  self
     */
    public function setAuthAttempts($authAttempts)
    {
        $this->authAttempts = $authAttempts;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getAuthAttempts()
    {
        return $this->authAttempts;
    }

    /**
     * Standard setter.
     *
     * @param   int $lockedUntil
     *
     * @return  self
     */
    public function setLockedUntil($lockedUntil)
    {
        $this->lockedUntil = $lockedUntil;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getLockedUntil()
    {
        return $this->lockedUntil;
    }

    /**
     * Standard setter.
     *
     * @param   string $locale
     *
     * @return  self
     */
    public function setLocale($locale)
    {
        /**
         * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
         */
        $locales = [
            'en_US' => 1000,
            'en_GB' => 1001,
            'en_CA' => 1002,
            'en_AU' => 1003,
            'en_NZ' => 1004,
            'fr'    => 1010,
            'de'    => 1020,
            'it'    => 1030,
            'es'    => 1040,
            'pt_BR' => 1080,
            'nl'    => 1090,
            'sv'    => 2020,
            'lv'    => 2050,
            'ru'    => 3000,
            'pl'    => 3030,
            'cs'    => 3040,
            'hu'    => 3060,
            'bg'    => 3130,
            'ro'    => 3140,
            'ja'    => 5000,
            'tr'    => 6000,
        ];

        if (!array_key_exists($locale, $locales)) {
            $locale = 'en_US';
        }

        $this->locale = $locales[$locale];

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getLocale()
    {
        /**
         * @deprecated 4.1.0 A stub for compatibility btw 3.6 and 4.0.
         */
        $locales = [
            1000 => 'en_US',
            1001 => 'en_GB',
            1002 => 'en_CA',
            1003 => 'en_AU',
            1004 => 'en_NZ',
            1010 => 'fr',
            1020 => 'de',
            1030 => 'it',
            1040 => 'es',
            1080 => 'pt_BR',
            1090 => 'nl',
            2020 => 'sv',
            2050 => 'lv',
            3000 => 'ru',
            3030 => 'pl',
            3040 => 'cs',
            3060 => 'hu',
            3130 => 'bg',
            3140 => 'ro',
            5000 => 'ja',
            6000 => 'tr',
        ];

        if (!array_key_exists($this->locale, $locales)) {
            $this->locale = 1000;
        }

        return $locales[$this->locale];
    }

    /**
     * Standard setter.
     *
     * @param   int $timezone
     *
     * @return  self
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Standard setter.
     *
     * @param   int $viewId
     *
     * @return  self
     */
    public function setViewId($viewId)
    {
        $this->viewId = $viewId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getViewId()
    {
        return $this->viewId;
    }

    /**
     * Standard setter.
     *
     * @param   string $theme
     *
     * @return  self
     */
    public function setTheme($theme)
    {
        if (in_array($theme, Theme::getAllKeys())) {
            $this->theme = $theme;
        }

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  string
     */
    public function getTheme()
    {
        $theme = strtolower($this->theme);

        if (!in_array($theme, Theme::getAllKeys())) {
            $theme = 'azure';
        }

        return $theme;
    }

    /**
     * Get list of groups the user is member of.
     *
     * @return  ArrayCollection|Group[]
     */
    public function getGroups()
    {
        return $this->groups;
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

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
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
        return $this->lockedUntil < time();
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
        return !$this->isDisabled;
    }

    /**
     * Returns authentication source of the user.
     *
     * @return  string
     */
    public function getAuthenticationSource()
    {
        return $this->isLdap ? 'LDAP' : 'eTraxis';
    }
}
