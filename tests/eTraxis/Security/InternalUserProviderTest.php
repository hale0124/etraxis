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

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\CurrentUser;
use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;
use Symfony\Component\Security\Core\User\User as SymfonyUser;

class InternalUserProviderTest extends TransactionalTestCase
{
    /** @var InternalUserProvider */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();

        $this->object = new InternalUserProvider($this->logger, $manager);
    }

    public function testLoadInternalUserByUsername()
    {
        $result = $this->object->loadUserByUsername('artem');

        self::assertInstanceOf(CurrentUser::class, $result);
        self::assertEquals('Artem Rodygin', $result->getFullname());
        self::assertFalse($result->isExternalAccount());
    }

    public function testLoadLdapUserByUsername()
    {
        $result = $this->object->loadUserByUsername('einstein');

        self::assertInstanceOf(CurrentUser::class, $result);
        self::assertEquals('Albert Einstein', $result->getFullname());
        self::assertTrue($result->isExternalAccount());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testUsernameNotFoundException()
    {
        $this->object->loadUserByUsername('user404');
    }

    public function testRefreshUser()
    {
        $user = new User(AuthenticationProvider::ETRAXIS);

        $user->setUsername('artem');

        $result = $this->object->refreshUser(new CurrentUser($user));

        self::assertInstanceOf(CurrentUser::class, $result);
        self::assertEquals('Artem Rodygin', $result->getFullname());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testUnsupportedUserException()
    {
        $user = new SymfonyUser('artem', 'secret');

        $this->object->refreshUser($user);
    }

    public function testSupportsClass()
    {
        self::assertFalse($this->object->supportsClass(SymfonyUser::class));
        self::assertTrue($this->object->supportsClass(CurrentUser::class));
    }
}
