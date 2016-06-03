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
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Tests\TransactionalTestCase;

class UserTest extends TransactionalTestCase
{
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
        $user = new User();
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
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        $this->object->setPassword('Password1', 1);
        self::assertEquals('Password1', $this->object->getPassword());
        self::assertGreaterThan(0, $object->passwordExpiresAt - time());

        $this->object->setPassword('Password2');
        self::assertEquals('Password2', $this->object->getPassword());
        self::assertNull($object->passwordExpiresAt);
    }

    public function testIsPasswordExpired()
    {
        $this->object->setPassword('secret', 1);
        self::assertFalse($this->object->isPasswordExpired());

        $this->object->setPassword('secret', 0);
        self::assertTrue($this->object->isPasswordExpired());

        $this->object->setPassword('secret');
        self::assertFalse($this->object->isPasswordExpired());
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
        self::assertFalse($this->object->isLocked());

        $this->object->lock(2, 30);
        self::assertTrue($this->object->isLocked());

        $this->object->unlock();
        self::assertFalse($this->object->isLocked());
    }

    public function testIsLocked()
    {
        /** @var \StdClass $object */
        $object = AltrEgo::create($this->object);

        self::assertFalse($this->object->isLocked());

        $object->lockedUntil = time() + 5;
        self::assertTrue($this->object->isLocked());

        $object->lockedUntil = time() - 1;
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
        self::assertEquals('Hubert J. Farnsworth', (string) $this->object);
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
