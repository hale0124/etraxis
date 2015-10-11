<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Rhumsaa\Uuid\Uuid;

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
    private $object = null;

    protected function setUp()
    {
        $this->object = new UserStub();
    }

    public function testId()
    {
        $this->assertEquals(null, $this->object->getId());
    }

    public function testUsername()
    {
        $expected = 'Username';
        $this->object->setUsername($expected);
        $this->assertEquals($expected, $this->object->getUsername());
    }

    public function testFullname()
    {
        $expected = 'Fullname';
        $this->object->setFullname($expected);
        $this->assertEquals($expected, $this->object->getFullname());
    }

    public function testEmail()
    {
        $expected = 'Email';
        $this->object->setEmail($expected);
        $this->assertEquals($expected, $this->object->getEmail());
    }

    public function testDescription()
    {
        $expected = 'Description';
        $this->object->setDescription($expected);
        $this->assertEquals($expected, $this->object->getDescription());
    }

    public function testPassword()
    {
        $expected = 'Password';
        $this->object->setPassword($expected);
        $this->assertEquals($expected, $this->object->getPassword());
    }

    public function testPasswordSetAt()
    {
        $expected = time();
        $this->object->setPasswordSetAt($expected);
        $this->assertEquals($expected, $this->object->getPasswordSetAt());
    }

    public function testResetToken()
    {
        $expected = Uuid::uuid4()->getHex();
        $this->object->setResetToken($expected);
        $this->assertEquals($expected, $this->object->getResetToken());
    }

    public function testResetTokenExpiresAt()
    {
        $expected = time();
        $this->object->setResetTokenExpiresAt($expected);
        $this->assertEquals($expected, $this->object->getResetTokenExpiresAt());
    }

    public function testIsAdmin()
    {
        $this->object->setAdmin(false);
        $this->assertFalse($this->object->isAdmin());

        $this->object->setAdmin(true);
        $this->assertTrue($this->object->isAdmin());
    }

    public function testIsDisabled()
    {
        $this->object->setDisabled(false);
        $this->assertFalse($this->object->isDisabled());

        $this->object->setDisabled(true);
        $this->assertTrue($this->object->isDisabled());
    }

    public function testIsLdap()
    {
        $this->object->setLdap(false);
        $this->assertFalse($this->object->isLdap());

        $this->object->setLdap(true);
        $this->assertTrue($this->object->isLdap());
    }

    public function testAuthAttempts()
    {
        $expected = 0;
        $this->object->setAuthAttempts($expected);
        $this->assertEquals($expected, $this->object->getAuthAttempts());
    }

    public function testLockedUntil()
    {
        $expected = time();
        $this->object->setLockedUntil($expected);
        $this->assertEquals($expected, $this->object->getLockedUntil());
    }

    public function testLocale()
    {
        $expected = 'ru';
        $this->object->setLocale($expected);
        $this->assertEquals($expected, $this->object->getLocale());
    }

    public function testGetLocaleFallback()
    {
        $expected             = 'en_US';
        $this->object->locale = 0;
        $this->assertEquals($expected, $this->object->getLocale());
    }

    public function testSetLocaleFallback()
    {
        $expected = 'en_US';
        $this->object->setLocale('xx-XX');
        $this->assertEquals($expected, $this->object->getLocale());
    }

    public function testTimezone()
    {
        $expected = mt_rand();
        $this->object->setTimezone($expected);
        $this->assertEquals($expected, $this->object->getTimezone());
    }

    public function testViewId()
    {
        $expected = mt_rand();
        $this->object->setViewId($expected);
        $this->assertEquals($expected, $this->object->getViewId());
    }

    public function testTheme()
    {
        $expected = 'emerald';
        $this->object->setTheme($expected);
        $this->assertEquals($expected, $this->object->getTheme());
    }

    public function testThemeUnsupported()
    {
        $expected = 'azure';
        $this->object->setTheme('unsupported');
        $this->assertEquals($expected, $this->object->getTheme());
    }

    public function testGroups()
    {
        $this->assertCount(0, $this->object->getGroups());
    }

    public function testGetRolesAsAdmin()
    {
        $this->object->setAdmin(true);
        $roles = $this->object->getRoles();

        $this->assertTrue(in_array('ROLE_USER', $roles));
        $this->assertTrue(in_array('ROLE_ADMIN', $roles));
    }

    public function testGetRolesAsUser()
    {
        $this->object->setAdmin(false);
        $roles = $this->object->getRoles();

        $this->assertTrue(in_array('ROLE_USER', $roles));
        $this->assertFalse(in_array('ROLE_ADMIN', $roles));
    }

    public function testGetSalt()
    {
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        $this->assertNull($this->object->getSalt());
    }

    public function testEraseCredentials()
    {
        $this->object->eraseCredentials();
    }

    public function testIsAccountNonExpired()
    {
        $this->assertTrue($this->object->isAccountNonExpired());
    }

    public function testIsAccountNonLocked()
    {
        $this->assertTrue($this->object->isAccountNonLocked());

        $this->object->setLockedUntil(time() + 5);
        $this->assertFalse($this->object->isAccountNonLocked());

        $this->object->setLockedUntil(time() - 1);
        $this->assertTrue($this->object->isAccountNonLocked());
    }

    public function testIsCredentialsNonExpired()
    {
        $this->assertTrue($this->object->isCredentialsNonExpired());
    }

    public function testIsEnabled()
    {
        $this->object->setDisabled(false);
        $this->assertTrue($this->object->isEnabled());

        $this->object->setDisabled(true);
        $this->assertFalse($this->object->isEnabled());
    }

    public function testGetAuthenticationSource()
    {
        $this->object->setLdap(false);
        $this->assertEquals('eTraxis', $this->object->getAuthenticationSource());

        $this->object->setLdap(true);
        $this->assertEquals('LDAP', $this->object->getAuthenticationSource());
    }
}
