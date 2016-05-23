<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends ControllerTestCase
{
    public function testIndexAction()
    {
        $uri = $this->router->generate('admin');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FOUND);
        $this->assertLocationHeader($this->router->generate('admin_users'));
    }
}
