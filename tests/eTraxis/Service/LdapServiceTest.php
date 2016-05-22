<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Tests\TransactionalTestCase;

/**
 * "http://www.forumsys.com/tutorials/integration-how-to/ldap/online-ldap-test-server".
 */
class LdapServiceTest extends TransactionalTestCase
{
    /** @var Ldap\LdapService */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new Ldap\LdapService($this->logger,
            'ldap.forumsys.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password'
        );
    }

    /**
     * @expectedException \eTraxis\Service\Ldap\LdapException
     */
    public function testConnectionHost()
    {
        new Ldap\LdapService($this->logger,
            'ldap.example.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password'
        );
    }

    /**
     * @expectedException \eTraxis\Service\Ldap\LdapException
     */
    public function testConnectionTls()
    {
        new Ldap\LdapService($this->logger,
            'ldap.forumsys.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password',
            true
        );
    }

    public function testFindSuccess()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein', ['cn', 'mail']);

        self::assertTrue(is_array($entry));

        self::assertArrayHasKey('cn', $entry);
        self::assertArrayHasKey('mail', $entry);

        self::assertEquals('Albert Einstein', $entry['cn']);
        self::assertEquals('einstein@ldap.forumsys.com', $entry['mail']);
    }

    public function testFindFailure()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein404', ['cn', 'mail']);

        self::assertFalse($entry);
    }

    public function testFindBadSearch()
    {
        $entry = $this->object->find('DC=invalid,DC=com', 'einstein', ['cn', 'mail']);

        self::assertFalse($entry);
    }

    public function testFindBadAttribute()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein', ['cn', 'mail', 'invalid']);

        self::assertFalse($entry);
    }

    public function testAuthenticateSuccess()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein', 'password');

        self::assertTrue($result);
    }

    public function testAuthenticateFailureUsername()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein404', 'password');

        self::assertFalse($result);
    }

    public function testAuthenticateFailurePassword()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein', 'wrong');

        self::assertFalse($result);
    }
}
