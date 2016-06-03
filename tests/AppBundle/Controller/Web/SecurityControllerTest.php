<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Web;

use eTraxis\Entity\User;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends ControllerTestCase
{
    private function isAuthenticated(Crawler $crawler)
    {
        $text = $crawler->filter('nav a')->last()->html();

        if ($text === 'Log in') {
            return false;
        }

        if ($text === 'Log out') {
            return true;
        }

        throw new \RuntimeException('Authentication status is indeterminable');
    }

    private function isLoginPage(Crawler $crawler)
    {
        return
            count($crawler->filter('input[name="_username"]')) === 1 &&
            count($crawler->filter('input[name="_password"]')) === 1 &&
            count($crawler->filter('input[type="submit"]')) === 1;
    }

    public function testLoginAction()
    {
        $uri = $this->router->generate('login');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));
    }

    public function testForgotPasswordAction()
    {
        $uri = $this->router->generate('forgot_password');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));
    }

    public function testResetPasswordAction()
    {
        $uri = $this->router->generate('reset_password', [
            'token' => 'cbda95b3a78f4bd7a83c954544497a64',
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));
    }

    public function testSetExpiredPasswordAction()
    {
        $uri = $this->router->generate('set_expired_password');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertLoginPage();

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('homepage'));
    }

    public function testLogin()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'wrong',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'secret',
        ]);

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));

        $this->client->request(Request::METHOD_GET, '/login');

        self::assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->client->getContainer()->get('doctrine')->getManager();

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'artem']);
        $user->unlock();

        $manager->persist($user);
        $manager->flush();
    }
}
