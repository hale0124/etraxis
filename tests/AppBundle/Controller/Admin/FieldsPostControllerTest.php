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

use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FieldsPostControllerTest extends ControllerTestCase
{
    /** @var State */
    private $state;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->state = $doctrine->getRepository(State::class)->findOneBy([
            'name' => 'New',
        ]);
    }

    public function testNewAction()
    {
        $uri = $this->router->generate('admin_new_field', [
            'id' => $this->state->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testEditAction()
    {
        $uri = $this->router->generate('admin_edit_field', [
            'id' => 0,
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testPcreAction()
    {
        $uri = $this->router->generate('admin_pcre_field', [
            'id' => 0,
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testDeleteAction()
    {
        $uri = $this->router->generate('admin_delete_field', [
            'id' => 0,
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testOrderAction()
    {
        $uri = $this->router->generate('admin_set_field_order', [
            'id'    => 0,
            'order' => 0,
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testSaveRolePermissionsAction()
    {
        $uri = $this->router->generate('admin_fields_save_role_permissions', [
            'id'   => 0,
            'role' => SystemRole::AUTHOR,
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }

    public function testSaveGroupPermissionsAction()
    {
        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        /** @var Group $group */
        $group = $doctrine->getRepository(Group::class)->findOneBy([
            'name' => 'Crew',
        ]);

        $uri = $this->router->generate('admin_fields_save_group_permissions', [
            'id'    => 0,
            'group' => $group->getId(),
        ]);

        $this->makeRequest(Request::METHOD_GET, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_UNAUTHORIZED);

        $this->loginAs('fry');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_FORBIDDEN);

        $this->loginAs('hubert');

        $this->makeRequest(Request::METHOD_POST, $uri, true);
        $this->assertStatusCode(Response::HTTP_BAD_REQUEST);
    }
}
