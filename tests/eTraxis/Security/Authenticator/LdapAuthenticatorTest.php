<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Security\Authenticator;

use eTraxis\Service\Ldap\LdapInterface;
use eTraxis\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LdapServiceStub implements LdapInterface
{
    public function find($basedn, $username, array $attributes = [])
    {
        if ($username !== 'einstein') {
            return false;
        }

        return [
            'cn'   => 'Albert Einstein',
            'mail' => 'einstein@ldap.forumsys.com',
        ];
    }

    public function authenticate($basedn, $username, $password)
    {
        return $username === 'einstein' && $password === 'password';
    }
}

class LdapAuthenticatorTest extends BaseTestCase
{
    /** @var LdapAuthenticator */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->logger,
            $this->command_bus,
            new LdapServiceStub(),
            'DC=example,DC=com'
        );
    }

    public function testTargetPathWeb()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', 'http://localhost/settings/');

        $this->object->start($request);

        self::assertEquals('http://localhost/settings/', $this->session->get('_security.main.target_path'));
    }

    public function testTargetPathAjax()
    {
        $request = new Request();
        $request->server->set('REQUEST_URI', 'http://localhost/settings/');
        $request->headers->add(['X-Requested-With' => 'XMLHttpRequest']);

        $this->object->start($request);

        self::assertNull($this->session->get('_security.main.target_path'));
    }

    public function testGetCredentialsSuccess()
    {
        $expected = [
            'username' => 'artem',
            'password' => 'secret',
        ];

        $request = new Request([], [
            '_username' => 'artem',
            '_password' => 'secret',
        ]);

        self::assertEquals($expected, $this->object->getCredentials($request));
    }

    public function testGetCredentialsFailure()
    {
        self::assertNull($this->object->getCredentials(new Request()));
    }

    public function testGetUserSuccess()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $this->object->getUser([
            'username' => 'einstein',
            'password' => 'password',
        ], $provider);

        self::assertInstanceOf('\\eTraxis\\Entity\\User', $user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testGetUserFailure()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $this->object->getUser([
            'username' => 'unknown',
            'password' => 'password',
        ], $provider);
    }

    public function testCheckCredentials()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $provider->loadUserByUsername('einstein');

        self::assertTrue($this->object->checkCredentials([
            'username' => 'einstein',
            'password' => 'password',
        ], $user));

        self::assertFalse($this->object->checkCredentials([
            'username' => 'einstein',
            'password' => 'wrong',
        ], $user));

        self::assertFalse($this->object->checkCredentials([
            'username' => 'unknown',
            'password' => 'password',
        ], $user));
    }

    public function testOnAuthenticationFailure()
    {
        self::assertNull($this->object->onAuthenticationFailure(new Request(), new AuthenticationException()));
    }

    public function testOnAuthenticationSuccess()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $provider->loadUserByUsername('einstein');

        $response = $this->object->onAuthenticationSuccess(
            new Request(),
            $this->object->createAuthenticatedToken($user, 'main'),
            'etraxis.provider'
        );

        self::assertInstanceOf(Response::class, $response);
    }

    public function testSupportsRememberMe()
    {
        self::assertTrue($this->object->supportsRememberMe());
    }
}
