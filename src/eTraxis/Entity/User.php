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
use eTraxis\Dictionary;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints as Assert;

/**
 * User.
 *
 * @ORM\Table(name="users",
 *            uniqueConstraints={
 *                @ORM\UniqueConstraint(name="ix_users_username", columns={"provider", "username"})
 *            })
 * @ORM\Entity
 * @ORM\EntityListeners({"eTraxis\Entity\EntityListener"})
 * @Assert\UniqueEntity(fields={"username"}, message="user.conflict.username")
 */
class User extends Entity implements \JsonSerializable
{
    // Constraints.
    const MAX_USERNAME    = 64;
    const MAX_FULLNAME    = 64;
    const MAX_EMAIL       = 320;
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
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var string Authentication provider.
     *
     * @ORM\Column(name="provider", type="string", length=20)
     */
    private $provider;

    /**
     * @var string User's login.
     *
     * @ORM\Column(name="username", type="string", length=64)
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
     * @ORM\Column(name="email", type="string", length=320)
     */
    private $email;

    /**
     * @var string Description of the user.
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var bool Whether user has administration privileges.
     *
     * @ORM\Column(name="is_admin", type="boolean")
     */
    private $isAdmin;

    /**
     * @var bool Whether user is disabled by administrator.
     *
     * @ORM\Column(name="is_disabled", type="boolean")
     */
    private $isDisabled;

    /**
     * @var string Password hash.
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=true)
     */
    private $password;

    /**
     * @var int Unix Epoch timestamp when the password expires.
     *
     * @ORM\Column(name="password_expires", type="integer", nullable=true)
     */
    private $passwordExpiresAt;

    /**
     * @var string Token for password reset.
     *
     * @ORM\Column(name="reset_token", type="string", length=32, nullable=true)
     */
    private $resetToken;

    /**
     * @var int Unix Epoch timestamp when the password reset token expires.
     *
     * @ORM\Column(name="reset_token_expires", type="integer", nullable=true)
     */
    private $resetTokenExpiresAt;

    /**
     * @var int Number of consecutive unsuccessful attempts to authenticate.
     *
     * @ORM\Column(name="auth_attempts", type="integer", nullable=true)
     */
    private $authAttempts;

    /**
     * @var int Unix Epoch timestamp which the account is locked till.
     *          If in the past, the account is considered as not locked.
     *
     * @ORM\Column(name="locked_until", type="integer", nullable=true)
     */
    private $lockedUntil;

    /**
     * @var array User's settings.
     *
     * @ORM\Column(name="settings", type="json_array", nullable=true)
     */
    private $settings;

    /**
     * @var ArrayCollection List of groups the user is member of.
     *
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="members")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $groups;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
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
     * @param   string $provider
     *
     * @return  self
     */
    public function setProvider(string $provider)
    {
        if (Dictionary\AuthenticationProvider::has($provider)) {
            $this->provider = $provider;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Returns TRUE, if this account is from external source (LDAP, OAuth, etc).
     *
     * @return  bool
     */
    public function isExternalAccount()
    {
        return $this->provider !== Dictionary\AuthenticationProvider::ETRAXIS;
    }

    /**
     * Property setter.
     *
     * @param   string $username
     *
     * @return  self
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Property setter.
     *
     * @param   string $fullname
     *
     * @return  self
     */
    public function setFullname(string $fullname)
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
    public function setEmail(string $email)
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
     * @param   string|null $description
     *
     * @return  self
     */
    public function setDescription(string $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Property setter.
     *
     * @param   bool $isAdmin
     *
     * @return  self
     */
    public function setAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Property setter.
     *
     * @param   bool $isDisabled
     *
     * @return  self
     */
    public function setDisabled(bool $isDisabled)
    {
        $this->isDisabled = $isDisabled;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  bool
     */
    public function isDisabled()
    {
        return $this->isDisabled;
    }

    /**
     * Sets user's password.
     *
     * @param   string $password Password hash.
     * @param   int    $days     Number of days a password is valid for (NULL for no expiration).
     *
     * @return  self
     */
    public function setPassword(string $password = null, int $days = null)
    {
        if (!$this->isExternalAccount()) {
            $this->password          = $password;
            $this->passwordExpiresAt = ($days === null) ? null : time() + $days * 86400;
        }

        return $this;
    }

    /**
     * Returns user's password.
     *
     * @return  string|null
     */
    public function getPassword()
    {
        return $this->isExternalAccount() ? null : $this->password;
    }

    /**
     * Checks whether user's password is expired.
     *
     * @return  bool
     */
    public function isPasswordExpired()
    {
        return !$this->isExternalAccount() && $this->passwordExpiresAt !== null && $this->passwordExpiresAt <= time();
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
        $this->resetTokenExpiresAt = null;

        return $this;
    }

    /**
     * Checks whether current "password reset" token is expired.
     *
     * @return  bool
     */
    public function isResetTokenExpired()
    {
        return $this->resetTokenExpiresAt === null || $this->resetTokenExpiresAt <= time();
    }

    /**
     * Increases locks count for the account.
     *
     * @param   int $max_auth_attempts Maximum number of attempts to log in.
     * @param   int $lock_time         Number of minutes to lock out for.
     *
     * @return  bool Whether the account became locked.
     */
    public function lock(int $max_auth_attempts, int $lock_time)
    {
        if (!$this->isLocked()) {

            $this->authAttempts++;

            if ($this->authAttempts >= $max_auth_attempts) {
                $this->authAttempts = null;
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
        $this->authAttempts = null;
        $this->lockedUntil  = null;
    }

    /**
     * Checks whether account is locked.
     *
     * @return  bool
     */
    public function isLocked()
    {
        return !$this->isExternalAccount() && $this->lockedUntil !== null && $this->lockedUntil >= time();
    }

    /**
     * Sets user's locale.
     *
     * @param   string $locale
     *
     * @return  self
     */
    public function setLocale(string $locale)
    {
        if (Dictionary\Locale::has($locale)) {
            $this->settings['locale'] = $locale;
        }

        return $this;
    }

    /**
     * Retrieves user's locale.
     *
     * @return  string
     */
    public function getLocale()
    {
        return $this->settings['locale'] ?? Dictionary\Locale::FALLBACK;
    }

    /**
     * Sets user's theme.
     *
     * @param   string $theme
     *
     * @return  self
     */
    public function setTheme(string $theme)
    {
        if (Dictionary\Theme::has($theme)) {
            $this->settings['theme'] = $theme;
        }

        return $this;
    }

    /**
     * Retrieves user's theme.
     *
     * @return  string
     */
    public function getTheme()
    {
        return $this->settings['theme'] ?? Dictionary\Theme::FALLBACK;
    }

    /**
     * Sets user's timezone.
     *
     * @param   string $timezone
     *
     * @return  self
     */
    public function setTimezone(string $timezone)
    {
        if (Dictionary\Timezone::has($timezone)) {
            $this->settings['timezone'] = $timezone;
        }

        return $this;
    }

    /**
     * Retrieves user's timezone.
     *
     * @return  string
     */
    public function getTimezone()
    {
        return $this->settings['timezone'] ?? Dictionary\Timezone::FALLBACK;
    }

    /**
     * Returns list of groups the user is member of.
     *
     * @return  Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Returns list of groups the user is not a member of.
     *
     * @return  Group[]
     */
    public function getOtherGroups()
    {
        $query = $this->manager->createQueryBuilder();

        $query
            ->select('g')
            ->addSelect('p')
            ->from(Group::class, 'g')
            ->leftJoin('g.project', 'p')
            ->orderBy('p.name')
            ->addOrderBy('g.name')
        ;

        if (count($this->groups) > 0) {
            $query
                ->where($query->expr()->notIn('g', ':groups'))
                ->setParameter('groups', $this->groups)
            ;
        }

        return $query->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getFullname();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'id'          => $this->getId(),
            'provider'    => $this->getProvider(),
            'username'    => $this->getUsername(),
            'fullname'    => $this->getFullname(),
            'email'       => $this->getEmail(),
            'description' => $this->getDescription(),
            'isAdmin'     => $this->isAdmin(),
            'isDisabled'  => $this->isDisabled(),
            'locale'      => $this->getLocale(),
            'theme'       => $this->getTheme(),
            'timezone'    => $this->getTimezone(),
        ];
    }
}
