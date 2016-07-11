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

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Tests\TransactionalTestCase;
use eTraxis\Traits\ReflectionTrait;

class UserTest extends TransactionalTestCase
{
    use ReflectionTrait;

    /** @var User */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => 'hubert',
        ]);
    }

    public function testId()
    {
        $user = new User(AuthenticationProvider::FALLBACK);
        self::assertNull($user->getId());
        self::assertNotNull($this->object->getId());
    }

    public function testProvider()
    {
        self::assertEquals(AuthenticationProvider::ETRAXIS, $this->object->getProvider());
        self::assertFalse($this->object->isExternalAccount());

        /** @var User $einstein */
        $einstein = $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => 'einstein',
        ]);

        self::assertEquals(AuthenticationProvider::LDAP, $einstein->getProvider());
        self::assertTrue($einstein->isExternalAccount());
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

    public function testPassword()
    {
        $this->object->setPassword('Password1');
        self::assertEquals('Password1', $this->object->getPassword());
        self::assertLessThan(1, $this->getProperty($this->object, 'passwordTimestamp') - time());
    }

    public function testIsPasswordExpired()
    {
        $this->object->setPassword('secret');
        $this->setProperty($this->object, 'passwordTimestamp', time() - 86500);

        self::assertFalse($this->object->isPasswordExpired(2));
        self::assertTrue($this->object->isPasswordExpired(1));
        self::assertFalse($this->object->isPasswordExpired(0));
        self::assertFalse($this->object->isPasswordExpired());
    }

    public function testResetToken()
    {
        $this->object->generateResetToken();
        self::assertNotNull($this->getProperty($this->object, 'resetToken'));
        self::assertNotEquals(0, $this->getProperty($this->object, 'resetTokenExpiresAt'));

        $this->object->clearResetToken();
        self::assertNull($this->getProperty($this->object, 'resetToken'));
        self::assertEquals(0, $this->getProperty($this->object, 'resetTokenExpiresAt'));
    }

    public function testIsResetTokenExpired()
    {
        $this->object->generateResetToken();
        self::assertFalse($this->object->isResetTokenExpired());

        $timestamp = $this->getProperty($this->object, 'resetTokenExpiresAt');
        $this->setProperty($this->object, 'resetTokenExpiresAt', $timestamp - 7200);
        self::assertTrue($this->object->isResetTokenExpired());
    }

    public function testLockUnlock()
    {
        $this->object->lock(2, 30);
        self::assertFalse($this->object->isLocked());

        $this->object->lock(2, 30);
        self::assertTrue($this->object->isLocked());

        $this->object->unlock();
        self::assertFalse($this->object->isLocked());
    }

    public function testIsLocked()
    {
        self::assertFalse($this->object->isLocked());

        $this->setProperty($this->object, 'lockedUntil', time() + 5);
        self::assertTrue($this->object->isLocked());

        $this->setProperty($this->object, 'lockedUntil', time() - 1);
        self::assertFalse($this->object->isLocked());
    }

    public function testLocale()
    {
        $expected = 'ru';
        $this->object->setLocale($expected);
        self::assertEquals($expected, $this->object->getLocale());
    }

    public function testTheme()
    {
        $expected = 'emerald';
        $this->object->setTheme($expected);
        self::assertEquals($expected, $this->object->getTheme());
    }

    public function testTimezone()
    {
        $expected = 'Pacific/Auckland';
        $this->object->setTimezone($expected);
        self::assertEquals($expected, $this->object->getTimezone());
    }

    public function testGroups()
    {
        $user = $this->findUser('hubert');

        $groups = array_map(function (Group $group) {
            return $group->getName();
        }, $user->getGroups());

        $expected = [
            'Crew',
            'Managers',
            'Planet Express, Inc.',
        ];

        self::assertEquals($expected, $groups);
    }

    public function testOtherGroups()
    {
        $user = $this->findUser('hubert');

        $groups = array_map(function (Group $group) {
            return $group->getName();
        }, $user->getOtherGroups());

        $expected = [
            'Nimbus',
            'Members',
            'Staff',
        ];

        self::assertEquals($expected, $groups);
    }

    public function testToString()
    {
        self::assertRegExp('/^user\#(\d+)$/', (string) $this->object);
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'provider'    => $this->object->getProvider(),
            'username'    => $this->object->getUsername(),
            'fullname'    => $this->object->getFullname(),
            'email'       => $this->object->getEmail(),
            'description' => $this->object->getDescription(),
            'isAdmin'     => $this->object->isAdmin(),
            'isDisabled'  => $this->object->isDisabled(),
            'locale'      => $this->object->getLocale(),
            'theme'       => $this->object->getTheme(),
            'timezone'    => $this->object->getTimezone(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
