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

use Ramsey\Uuid\Uuid;

/**
 * @todo Remove in 4.1
 */
class UserStub extends User
{
    public $locale;
}

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var UserStub */
    private $object;

    protected function setUp()
    {
        $this->object = new UserStub();
    }

    public function testId()
    {
        self::assertEquals(null, $this->object->getId());
    }

    public function testUsername()
    {
        $expected = 'Username';
        $this->object->setUsername($expected);
        self::assertEquals($expected, $this->object->getUsername());
    }

    public function testFullname()
    {
        $expected = 'Fullname';
        $this->object->setFullname($expected);
        self::assertEquals($expected, $this->object->getFullname());
    }

    public function testEmail()
    {
        $expected = 'Email';
        $this->object->setEmail($expected);
        self::assertEquals($expected, $this->object->getEmail());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        self::assertEquals($expected, $this->object->getDescription());
    }

    public function testPassword()
    {
        $expected = 'Password';
        $this->object->setPassword($expected);
        self::assertEquals($expected, $this->object->getPassword());
    }

    public function testPasswordSetAt()
    {
        $expected = time();
        $this->object->setPasswordSetAt($expected);
        self::assertEquals($expected, $this->object->getPasswordSetAt());
    }

    public function testResetToken()
    {
        $expected = Uuid::uuid4()->getHex();
        $this->object->setResetToken($expected);
        self::assertEquals($expected, $this->object->getResetToken());
    }

    public function testResetTokenExpiresAt()
    {
        $expected = time();
        $this->object->setResetTokenExpiresAt($expected);
        self::assertEquals($expected, $this->object->getResetTokenExpiresAt());
    }

    public function testIsAdmin()
    {
        $this->object->setAdmin(false);
        self::assertFalse($this->object->isAdmin());

        $this->object->setAdmin(true);
        self::assertTrue($this->object->isAdmin());
    }

    public function testIsDisabled()
    {
        $this->object->setDisabled(false);
        self::assertFalse($this->object->isDisabled());

        $this->object->setDisabled(true);
        self::assertTrue($this->object->isDisabled());
    }

    public function testIsLdap()
    {
        $this->object->setLdap(false);
        self::assertFalse($this->object->isLdap());

        $this->object->setLdap(true);
        self::assertTrue($this->object->isLdap());
    }

    public function testAuthAttempts()
    {
        $expected = 0;
        $this->object->setAuthAttempts($expected);
        self::assertEquals($expected, $this->object->getAuthAttempts());
    }

    public function testLockedUntil()
    {
        $expected = time();
        $this->object->setLockedUntil($expected);
        self::assertEquals($expected, $this->object->getLockedUntil());
    }

    public function testLocale()
    {
        $expected = 'ru';
        $this->object->setLocale($expected);
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testGetLocaleFallback()
    {
        $expected             = 'en_US';
        $this->object->locale = 0;
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testSetLocaleFallback()
    {
        $expected = 'en_US';
        $this->object->setLocale('xx-XX');
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testTimezone()
    {
        $expected = mt_rand();
        $this->object->setTimezone($expected);
        self::assertEquals($expected, $this->object->getTimezone());
    }

    public function testViewId()
    {
        $expected = mt_rand();
        $this->object->setViewId($expected);
        self::assertEquals($expected, $this->object->getViewId());
    }

    public function testTheme()
    {
        $expected = 'emerald';
        $this->object->setTheme($expected);
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testThemeUnsupported()
    {
        $expected = 'azure';
        $this->object->setTheme('unsupported');
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testGroups()
    {
        self::assertCount(0, $this->object->getGroups());
    }

    public function testGetRolesAsAdmin()
    {
        $this->object->setAdmin(true);
        $roles = $this->object->getRoles();

        self::assertTrue(in_array(User::ROLE_USER, $roles));
        self::assertTrue(in_array(User::ROLE_ADMIN, $roles));
    }

    public function testGetRolesAsUser()
    {
        $this->object->setAdmin(false);
        $roles = $this->object->getRoles();

        self::assertTrue(in_array(User::ROLE_USER, $roles));
        self::assertFalse(in_array(User::ROLE_ADMIN, $roles));
    }

    public function testGetSalt()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        self::assertNull($this->object->getSalt());
    }

    public function testEraseCredentials()
    {
        $this->object->eraseCredentials();
    }

    public function testIsAccountNonExpired()
    {
        self::assertTrue($this->object->isAccountNonExpired());
    }

    public function testIsAccountNonLocked()
    {
        self::assertTrue($this->object->isAccountNonLocked());

        $this->object->setLockedUntil(time() + 5);
        self::assertFalse($this->object->isAccountNonLocked());

        $this->object->setLockedUntil(time() - 1);
        self::assertTrue($this->object->isAccountNonLocked());
    }

    public function testIsCredentialsNonExpired()
    {
        self::assertTrue($this->object->isCredentialsNonExpired());
    }

    public function testIsEnabled()
    {
        $this->object->setDisabled(false);
        self::assertTrue($this->object->isEnabled());

        $this->object->setDisabled(true);
        self::assertFalse($this->object->isEnabled());
    }

    public function testGetAuthenticationSource()
    {
        $this->object->setLdap(false);
        self::assertEquals('eTraxis', $this->object->getAuthenticationSource());

        $this->object->setLdap(true);
        self::assertEquals('LDAP', $this->object->getAuthenticationSource());
    }
}
