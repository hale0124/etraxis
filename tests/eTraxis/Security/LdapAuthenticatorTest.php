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
use eTraxis\Entity\User;
use eTraxis\Tests\TransactionalTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LdapAuthenticatorTest extends TransactionalTestCase
{
    /** @var LdapAuthenticator */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->command_bus,
            new LdapStub(),
            'dc=example,dc=com',
            'cn=admin,dc=example,dc=com',
            'secret'
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

    public function testGetUser()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $this->object->getUser([
            'username' => 'einstein',
            'password' => 'password',
        ], $provider);

        self::assertInstanceOf(CurrentUser::class, $user);
    }

    public function testGetUnknownUser()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $this->object->getUser([
            'username' => 'unknown',
            'password' => 'password',
        ], $provider);

        self::assertNull($user);
    }

    public function testGetIncompleteUser()
    {
        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $this->object->getUser([
            'username' => 'artem',
            'password' => 'password',
        ], $provider);

        self::assertNull($user);
    }

    public function testGetUserInvalidAdmin()
    {
        $ldap = new LdapAuthenticator(
            $this->router,
            $this->session,
            $this->command_bus,
            new LdapStub(),
            'dc=example,dc=com',
            'cn=admin,dc=example,dc=com',
            'wrong'
        );

        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $ldap->getUser([
            'username' => 'einstein',
            'password' => 'password',
        ], $provider);

        self::assertNull($user);
    }

    public function testGetUserUnconfigured()
    {
        $ldap = new LdapAuthenticator($this->router, $this->session, $this->command_bus);

        /** @var \Symfony\Component\Security\Core\User\UserProviderInterface $provider */
        $provider = $this->client->getContainer()->get('etraxis.provider');

        $user = $ldap->getUser([
            'username' => 'einstein',
            'password' => 'password',
        ], $provider);

        self::assertNull($user);
    }

    public function testCheckCredentials()
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy([
            'provider' => AuthenticationProvider::LDAP,
            'username' => 'einstein',
        ]);

        self::assertTrue($this->object->checkCredentials([
            'username' => 'einstein',
            'password' => 'password',
        ], new CurrentUser($user)));

        self::assertFalse($this->object->checkCredentials([
            'username' => 'einstein',
            'password' => 'wrong',
        ], new CurrentUser($user)));

        self::assertFalse($this->object->checkCredentials([
            'username' => 'unknown',
            'password' => 'password',
        ], new CurrentUser($user)));
    }

    public function testCheckCredentialsUnconfigured()
    {
        $ldap = new LdapAuthenticator($this->router, $this->session, $this->command_bus);

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy([
            'provider' => AuthenticationProvider::LDAP,
            'username' => 'einstein',
        ]);

        self::assertFalse($ldap->checkCredentials([
            'username' => 'einstein',
            'password' => 'password',
        ], new CurrentUser($user)));
    }

    public function testOnAuthenticationFailure()
    {
        self::assertNull($this->object->onAuthenticationFailure(new Request(), new AuthenticationException()));
    }

    public function testOnAuthenticationSuccess()
    {
        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneBy([
            'provider' => AuthenticationProvider::LDAP,
            'username' => 'einstein',
        ]);

        $response = $this->object->onAuthenticationSuccess(
            new Request(),
            $this->object->createAuthenticatedToken(new CurrentUser($user), 'main'),
            'main'
        );

        self::assertInstanceOf(Response::class, $response);
    }

    public function testSupportsRememberMe()
    {
        self::assertTrue($this->object->supportsRememberMe());
    }

    public function testConnect()
    {
        $ldap = LdapAuthenticator::connect('example.com');

        self::assertInstanceOf(LdapInterface::class, $ldap);
    }

    public function testConnectUnconfigured()
    {
        $ldap = LdapAuthenticator::connect();

        self::assertNull($ldap);
    }
}
