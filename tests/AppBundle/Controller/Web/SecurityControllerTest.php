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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

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

        $crawler = $client->request('GET', '/login');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'wrong',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertTrue($this->isLoginPage($crawler));
        self::assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'secret',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));

        $client->request('GET', '/login');

        self::assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertFalse($this->isLoginPage($crawler));
        self::assertTrue($this->isAuthenticated($crawler));
    }
}
