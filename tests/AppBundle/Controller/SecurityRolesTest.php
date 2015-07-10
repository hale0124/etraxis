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
        '/'                               => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => true,  'ROLE_USER' => true,  'ROLE_ADMIN' => true],
        '/admin/'                         => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/'                   => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/ajax'               => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/{user}'             => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/{user}/tab/details' => ['METHOD' => Request::METHOD_GET,  'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/disable'            => ['METHOD' => Request::METHOD_POST, 'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/enable'             => ['METHOD' => Request::METHOD_POST, 'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
        '/admin/users/{user}/unlock'      => ['METHOD' => Request::METHOD_POST, 'ROLE_GUEST' => false, 'ROLE_USER' => false, 'ROLE_ADMIN' => true],
    ];

    /** @var \Symfony\Bundle\FrameworkBundle\Client */
    private $client = null;

    /** @var \eTraxis\Model\User */
    private $user = null;

    private function prepareUrl($url)
    {
        $url = str_replace('{user}', $this->user->getId(), $url);

        return $url;
    }

    private function login($role)
    {
        $token = new UsernamePasswordToken($this->user, 'secret', 'default', [$role]);

        $session = $this->client->getContainer()->get('session');
        $session->set('_security_default', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function setUp()
    {
        $this->client = static::createClient();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->user = $doctrine->getRepository('eTraxis:User')->findOneBy([
            'username' => 'artem@eTraxis',
            'isLdap'   => false,
        ]);
    }

    public function testGuest()
    {
        $this->client->request('GET', '/login');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        foreach ($this->urls as $url => $isAllowed) {

            $this->client->request($isAllowed['METHOD'], $this->prepareUrl($url));

            if ($isAllowed['ROLE_GUEST']) {
                $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
                $this->assertTrue($this->client->getResponse()->headers->has('location'));
                $location = $this->client->getResponse()->headers->get('location');
                $this->assertEquals('/login', substr($location, -6));
            }
        }
    }

    public function testUser()
    {
        $this->login('ROLE_USER');

        foreach ($this->urls as $url => $isAllowed) {

            $this->client->request($isAllowed['METHOD'], $this->prepareUrl($url));

            if ($isAllowed['ROLE_USER']) {
                $this->assertNotEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
            }
        }
    }

    public function testAdmin()
    {
        $this->login('ROLE_ADMIN');

        foreach ($this->urls as $url => $isAllowed) {

            $this->client->request($isAllowed['METHOD'], $this->prepareUrl($url));

            if ($isAllowed['ROLE_ADMIN']) {
                $this->assertNotEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
            }
            else {
                $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
            }
        }
    }
}
