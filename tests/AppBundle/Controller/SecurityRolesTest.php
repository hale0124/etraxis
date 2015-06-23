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

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityRolesTest extends WebTestCase
{
    private $urls = [
        '/'                 => ['ROLE_GUEST' => true,  'ROLE_USER' => true,  'ROLE_ADMIN' => true],
        '/admin/'           => ['ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/'     => ['ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/ajax' => ['ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
    ];

    public function testGuest()
    {
        $client = static::createClient();

        $client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        foreach ($this->urls as $url => $isAllowed) {

            $client->request(Request::METHOD_GET, $url);

            if ($isAllowed['ROLE_GUEST']) {
                $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
                $this->assertTrue($client->getResponse()->headers->has('location'));
                $location = $client->getResponse()->headers->get('location');
                $this->assertEquals('/login', substr($location, -6));
            }
        }
    }

    public function testUser()
    {
        $client = static::createClient();

        $token = new UsernamePasswordToken('artem', 'secret', 'default', ['ROLE_USER']);

        $session = $client->getContainer()->get('session');
        $session->set('_security_default', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        foreach ($this->urls as $url => $isAllowed) {

            $client->request(Request::METHOD_GET, $url);

            if ($isAllowed['ROLE_USER']) {
                $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
            }
        }
    }

    public function testAdmin()
    {
        $client = static::createClient();

        $token = new UsernamePasswordToken('artem', 'secret', 'default', ['ROLE_ADMIN']);

        $session = $client->getContainer()->get('session');
        $session->set('_security_default', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        foreach ($this->urls as $url => $isAllowed) {

            $client->request(Request::METHOD_GET, $url);

            if ($isAllowed['ROLE_ADMIN']) {
                $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
            }
        }
    }
}
