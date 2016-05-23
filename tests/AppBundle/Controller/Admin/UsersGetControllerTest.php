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

use eTraxis\Entity\User;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UsersGetControllerTest extends ControllerTestCase
{
    /** @var User */
    private $user;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->findUser('artem');
    }

    public function testIndexAction()
    {
        $uri = $this->router->generate('admin_users');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testListAction()
    {
        $uri = $this->router->generate('admin_users_list');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testCsvAction()
    {
        $uri = $this->router->generate('admin_users_csv');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testViewAction()
    {
        $uri = $this->router->generate('admin_view_user', [
            'id' => $this->user->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertLoginPage();

        $this->makeRequest(Request::METHOD_POST, $uri);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testTabDetailsAction()
    {
        $uri = $this->router->generate('admin_tab_user_details', [
            'id' => $this->user->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testTabGroupsAction()
    {
        $uri = $this->router->generate('admin_tab_user_groups', [
            'id' => $this->user->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testNewAction()
    {
        $uri = $this->router->generate('admin_dlg_new_user');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_OK);
    }

    public function testEditAction()
    {
        $uri = $this->router->generate('admin_dlg_edit_user', [
            'id' => $this->user->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_OK);
    }
}
