<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security;

use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;

class CurrentUserTest extends TransactionalTestCase
{
    /** @var User */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => 'artem',
        ]);
    }

    public function testId()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getId(), $user->getId());
    }

    public function testExternalAccount()
    {
        $user = new CurrentUser($this->object);
        self::assertFalse($user->isExternalAccount());

        /** @var User $einstein */
        $einstein = $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => 'einstein',
        ]);

        $user = new CurrentUser($einstein);
        self::assertTrue($user->isExternalAccount());
    }

    public function testFullname()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getFullname(), $user->getFullname());
    }

    public function testLocale()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getLocale(), $user->getLocale());
    }

    public function testTheme()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getTheme(), $user->getTheme());
    }

    public function testTimezone()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getTimezone(), $user->getTimezone());
    }

    public function testRolesAsAdmin()
    {
        $this->object->setAdmin(true);
        $user = new CurrentUser($this->object);

        self::assertTrue(in_array(CurrentUser::ROLE_USER, $user->getRoles()));
        self::assertTrue(in_array(CurrentUser::ROLE_ADMIN, $user->getRoles()));
    }

    public function testRolesAsUser()
    {
        $this->object->setAdmin(false);
        $user = new CurrentUser($this->object);

        self::assertTrue(in_array(CurrentUser::ROLE_USER, $user->getRoles()));
        self::assertFalse(in_array(CurrentUser::ROLE_ADMIN, $user->getRoles()));
    }

    public function testPassword()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getPassword(), $user->getPassword());
    }

    public function testSalt()
    {
        $user = new CurrentUser($this->object);

        self::assertNull($user->getSalt());
    }

    public function testUsername()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getUsername(), $user->getUsername());
    }

    public function testEraseCredentials()
    {
        $user = new CurrentUser($this->object);

        self::assertNotNull($user->getPassword());
        $user->eraseCredentials();
        self::assertNull($user->getPassword());
    }

    public function testIsAccountNonExpired()
    {
        $user = new CurrentUser($this->object);

        self::assertTrue($user->isAccountNonExpired());
    }

    public function testIsAccountNonLocked()
    {
        $this->object->lock(1, 30);
        $user = new CurrentUser($this->object);
        self::assertFalse($user->isAccountNonLocked());

        $this->object->unlock();
        $user = new CurrentUser($this->object);
        self::assertTrue($user->isAccountNonLocked());
    }

    public function testIsCredentialsNonExpired()
    {
        $user = new CurrentUser($this->object);

        self::assertTrue($user->isCredentialsNonExpired());
    }

    public function testIsEnabled()
    {
        $this->object->setDisabled(false);
        $user = new CurrentUser($this->object);
        self::assertTrue($user->isEnabled());

        $this->object->setDisabled(true);
        $user = new CurrentUser($this->object);
        self::assertFalse($user->isEnabled());
    }
}
