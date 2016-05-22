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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
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

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request(Request::METHOD_GET, '/login');

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'wrong',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'secret',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));

        $client->request(Request::METHOD_GET, '/login');

        self::assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        self::assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine')->getManager();

        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['username' => 'artem@eTraxis']);
        $user->unlock();

        $manager->persist($user);
        $manager->flush();
    }
}
