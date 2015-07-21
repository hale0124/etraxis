<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service;

use eTraxis\Tests\BaseTestCase;

/**
 * "http://www.forumsys.com/tutorials/integration-how-to/ldap/online-ldap-test-server".
 */
class LdapServiceTest extends BaseTestCase
{
    /** @var LdapService */
    private $object = null;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new LdapService($this->logger,
            'ldap.forumsys.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password'
        );
    }

    /**
     * @expectedException \eTraxis\Service\LdapException
     */
    public function testConnectionHost()
    {
        new LdapService($this->logger,
            'ldap.example.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password'
        );
    }

    /**
     * @expectedException \eTraxis\Service\LdapException
     */
    public function testConnectionTls()
    {
        new LdapService($this->logger,
            'ldap.forumsys.com',
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password',
            true
        );
    }

    public function testIsConnectedFalse()
    {
        $ldap = new LdapService($this->logger,
            null,
            389,
            'CN=read-only-admin,DC=example,DC=com',
            'password'
        );

        $this->assertFalse($ldap->isConnected());
    }

    public function testIsConnectedTrue()
    {
        $this->assertTrue($this->object->isConnected());
    }

    public function testFindSuccess()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein', ['cn', 'mail']);

        $this->assertTrue(is_array($entry));

        $this->assertArrayHasKey('cn', $entry);
        $this->assertArrayHasKey('mail', $entry);

        $this->assertEquals('Albert Einstein', $entry['cn']);
        $this->assertEquals('einstein@ldap.forumsys.com', $entry['mail']);
    }

    public function testFindFailure()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein404', ['cn', 'mail']);

        $this->assertFalse($entry);
    }

    public function testFindBadSearch()
    {
        $entry = $this->object->find('DC=invalid,DC=com', 'einstein', ['cn', 'mail']);

        $this->assertFalse($entry);
    }

    public function testFindBadAttribute()
    {
        $entry = $this->object->find('DC=example,DC=com', 'einstein', ['cn', 'mail', 'invalid']);

        $this->assertFalse($entry);
    }

    public function testAuthenticateSuccess()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein', 'password');

        $this->assertTrue($result);
    }

    public function testAuthenticateFailureUsername()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein404', 'password');

        $this->assertFalse($result);
    }

    public function testAuthenticateFailurePassword()
    {
        $result = $this->object->authenticate('DC=example,DC=com', 'einstein', 'wrong');

        $this->assertFalse($result);
    }
}
