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

use eTraxis\Tests\TransactionalTestCase;

class CurrentUserTest extends TransactionalTestCase
{
    /** @var User */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = $this->doctrine->getRepository(User::class)->findOneBy([
            'username' => 'artem@eTraxis',
        ]);
    }

    public function testId()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getId(), $user->getId());
    }

    public function testFullname()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getFullname(), $user->getFullname());
    }

    public function testIsLdap()
    {
        $this->object->setLdap(true);
        $user = new CurrentUser($this->object);
        self::assertTrue($user->isLdap());

        $this->object->setLdap(false);
        $user = new CurrentUser($this->object);
        self::assertFalse($user->isLdap());
    }

    public function testLocale()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getSettings()->getLocale(), $user->getLocale());
    }

    public function testTheme()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getSettings()->getTheme(), $user->getTheme());
    }

    public function testTimezone()
    {
        $user = new CurrentUser($this->object);

        self::assertEquals($this->object->getSettings()->getTimezone(), $user->getTimezone());
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
