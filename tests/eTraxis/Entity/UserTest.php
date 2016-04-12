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

use AltrEgo\AltrEgo;
use eTraxis\Collection\Timezone;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User */
    private $object;

    protected function setUp()
    {
        $this->object = new User();
    }

    public function testId()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected   = mt_rand(1, PHP_INT_MAX);
        $object->id = $expected;
        self::assertEquals($expected, $this->object->getId());
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
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected = 'Password';
        self::assertGreaterThan(1, time() - $object->passwordSetAt);
        $this->object->setPassword($expected);
        self::assertEquals($expected, $this->object->getPassword());
        self::assertLessThanOrEqual(1, time() - $object->passwordSetAt);
    }

    public function testIsPasswordExpired()
    {
        $this->object->setPassword('secret');

        self::assertFalse($this->object->isPasswordExpired(1));
        self::assertTrue($this->object->isPasswordExpired(0));
    }

    public function testResetToken()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $this->object->generateResetToken();
        self::assertNotNull($object->resetToken);
        self::assertNotEquals(0, $object->resetTokenExpiresAt);

        $this->object->clearResetToken();
        self::assertNull($object->resetToken);
        self::assertEquals(0, $object->resetTokenExpiresAt);
    }

    public function testIsResetTokenExpired()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $this->object->generateResetToken();
        self::assertFalse($this->object->isResetTokenExpired());

        $object->resetTokenExpiresAt -= 7200;
        self::assertTrue($this->object->isResetTokenExpired());
    }

    public function testLockUnlock()
    {
        $this->object->lock(2, 30);
        self::assertTrue($this->object->isAccountNonLocked());

        $this->object->lock(2, 30);
        self::assertFalse($this->object->isAccountNonLocked());

        $this->object->unlock();
        self::assertTrue($this->object->isAccountNonLocked());
    }

    public function testIsAccountNonLocked()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        self::assertTrue($this->object->isAccountNonLocked());

        $object->lockedUntil = time() + 5;
        self::assertFalse($this->object->isAccountNonLocked());

        $object->lockedUntil = time() - 1;
        self::assertTrue($this->object->isAccountNonLocked());
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

    public function testIsEnabled()
    {
        $this->object->setDisabled(false);
        self::assertTrue($this->object->isEnabled());

        $this->object->setDisabled(true);
        self::assertFalse($this->object->isEnabled());
    }

    public function testIsLdap()
    {
        $this->object->setLdap(false);
        self::assertFalse($this->object->isLdap());

        $this->object->setLdap(true);
        self::assertTrue($this->object->isLdap());
    }

    public function testGetAuthenticationSource()
    {
        $this->object->setLdap(false);
        self::assertEquals(User::AUTH_INTERNAL, $this->object->getAuthenticationSource());

        $this->object->setLdap(true);
        self::assertEquals(User::AUTH_LDAP, $this->object->getAuthenticationSource());
    }

    public function testLocale()
    {
        $expected = 'ru';
        $this->object->setLocale($expected);
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testGetLocaleFallback()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected       = 'en_US';
        $object->locale = 0;
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testSetLocaleFallback()
    {
        $expected = 'en_US';
        $this->object->setLocale('xx-XX');
        self::assertEquals($expected, $this->object->getLocale());
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

    public function testTimezone()
    {
        $timezones = array_flip(Timezone::getCollection());
        $expected  = $timezones['Pacific/Auckland'];

        $this->object->setTimezone($expected);
        self::assertEquals($expected, $this->object->getTimezone());

        $this->object->setTimezone(PHP_INT_MAX);
        self::assertEquals($expected, $this->object->getTimezone());
    }

    public function testTimezoneUnsupported()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $expected         = 0;
        $object->timezone = PHP_INT_MAX;

        self::assertEquals(PHP_INT_MAX, $object->timezone);
        self::assertEquals($expected, $this->object->getTimezone());
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
}
