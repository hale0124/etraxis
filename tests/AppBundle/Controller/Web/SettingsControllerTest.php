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

use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $uri = $this->router->generate('settings');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testAppearanceAction()
    {
        $uri = $this->router->generate('settings_appearance');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertLoginPage();

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('settings'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('settings'));
    }

    public function testPasswordAction()
    {
        $uri = $this->router->generate('settings_password');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertLoginPage();

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('settings'));

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('settings'));
    }
}
