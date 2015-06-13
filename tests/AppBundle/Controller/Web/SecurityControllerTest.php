<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------


namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class SecurityControllerTest extends WebTestCase
{
    private function isAuthenticated(Crawler $crawler)
    {
        if (count($crawler->filter('a[href="/login"]')) === 1) {
            return false;
        }

        if (count($crawler->filter('a[id="menu_logout"]')->html()) === 1) {
            return true;
        }

        throw new \Exception('Authentication status is indeterminable');
    }

    private function isLoginPage(Crawler $crawler)
    {
        if (count($crawler->filter('input[name="_username"]')) === 1 &&
            count($crawler->filter('input[name="_password"]')) === 1 &&
            count($crawler->filter('input[type="submit"]')) === 1)
        {
            return true;
        }

        return false;
    }

    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isLoginPage($crawler));
        $this->assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'wrong',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isLoginPage($crawler));
        $this->assertFalse($this->isAuthenticated($crawler));

        $form = $crawler->filter('input[type="submit"]')->form([
            '_username' => 'artem',
            '_password' => 'secret',
        ]);

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertFalse($this->isLoginPage($crawler));
        $this->assertTrue($this->isAuthenticated($crawler));

        $client->request('GET', '/login');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertFalse($this->isLoginPage($crawler));
        $this->assertTrue($this->isAuthenticated($crawler));
    }
}