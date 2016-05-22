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
use eTraxis\Tests\TransactionalTestCase;

class UserTest extends TransactionalTestCase
{
    /** @var User */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getManager()->getRepository(User::class)->findOneBy([
            'username' => 'hubert@eTraxis',
        ]);
    }

    public function testId()
    {
        $user = new User();
        self::assertNull($user->getId());
        self::assertNotNull($this->object->getId());
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

    public function testGetAuthenticationSource()
    {
        $this->object->setLdap(false);
        self::assertEquals(User::AUTH_INTERNAL, $this->object->getAuthenticationSource());

        $this->object->setLdap(true);
        self::assertEquals(User::AUTH_LDAP, $this->object->getAuthenticationSource());
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
            'Staff',
        ];

        self::assertEquals($expected, $groups);
    }

    public function testSettings()
    {
        self::assertNotNull($this->object->getSettings());
        self::assertInstanceOf(UserSettings::class, $this->object->getSettings());
    }

    public function testJsonSerialize()
    {
        $expected = [
            'id'          => $this->object->getId(),
            'username'    => $this->object->getUsername(),
            'fullname'    => $this->object->getFullname(),
            'email'       => $this->object->getEmail(),
            'description' => $this->object->getDescription(),
            'isAdmin'     => $this->object->isAdmin(),
            'isDisabled'  => $this->object->isDisabled(),
            'isLdap'      => $this->object->isLdap(),
            'locale'      => $this->object->getSettings()->getLocale(),
            'theme'       => $this->object->getSettings()->getTheme(),
            'timezone'    => $this->object->getSettings()->getTimezone(),
        ];

        self::assertEquals($expected, $this->object->jsonSerialize());
    }
}
