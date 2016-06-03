<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Tests;

use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ControllerTestCaseTest extends ControllerTestCase
{
    public function testMakeRequest()
    {
        self::assertNull($this->client->getResponse());

        $this->makeRequest(Request::METHOD_GET, $this->router->generate('login'));

        self::assertNotNull($this->client->getResponse());
    }

    public function testAssertStatusCode()
    {
        $this->loginAs('bender');
        $this->client->request(Request::METHOD_GET, $this->router->generate('admin_users'));
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);
    }

    public function testAssertLocationHeader()
    {
        $this->loginAs('bender');
        $this->client->request(Request::METHOD_GET, $this->router->generate('login'));
        $this->assertLocationHeader($this->router->generate('homepage'));
    }

    public function testAssertLoginPage()
    {
        $this->client->request(Request::METHOD_GET, $this->router->generate('homepage'));
        $this->assertLoginPage();
    }

    public function testFindUser()
    {
        self::assertNull($this->findUser('unknown'));

        $user = $this->findUser('artem');

        self::assertInstanceOf(User::class, $user);
        self::assertEquals('artem', $user->getUsername());
    }

    public function testLoginAs()
    {
        self::assertFalse($this->loginAs('unknown'));
        self::assertTrue($this->loginAs('artem'));
        self::assertTrue($this->loginAs('einstein', AuthenticationProvider::LDAP));
    }
}
