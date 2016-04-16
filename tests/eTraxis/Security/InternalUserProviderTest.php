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

use eTraxis\Entity\User as eTraxisUser;
use eTraxis\Tests\BaseTestCase;
use Symfony\Component\Security\Core\User\User as SymfonyUser;

class InternalUserProviderTest extends BaseTestCase
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

        self::assertInstanceOf(eTraxisUser::class, $result);
        self::assertEquals('artem@example.com', $result->getEmail());
        self::assertFalse($result->isLdap());
    }

    public function testLoadLdapUserByUsername()
    {
        $result = $this->object->loadUserByUsername('einstein');

        self::assertInstanceOf(eTraxisUser::class, $result);
        self::assertEquals('einstein@ldap.forumsys.com', $result->getEmail());
        self::assertTrue($result->isLdap());
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
        $user = new eTraxisUser();

        $user->setUsername('artem');

        $result = $this->object->refreshUser($user);

        self::assertInstanceOf(eTraxisUser::class, $result);
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
        self::assertTrue($this->object->supportsClass(eTraxisUser::class));
    }
}
