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
use eTraxis\Entity\Field;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FieldsGetControllerTest extends ControllerTestCase
{
    /** @var State */
    private $state;

    /** @var Field */
    private $field;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->state = $doctrine->getRepository(State::class)->findOneBy([
            'name' => 'New',
        ]);

        $this->field = $doctrine->getRepository(Field::class)->findOneBy([
            'name' => 'Crew',
        ]);
    }

    public function testListAction()
    {
        $uri = $this->router->generate('admin_fields_list', [
            'id' => $this->state->getId(),
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

    public function testViewAction()
    {
        $uri = $this->router->generate('admin_view_field', [
            'id' => $this->field->getId(),
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

    public function testTabDetailsAction()
    {
        $uri = $this->router->generate('admin_tab_field_details', [
            'id' => $this->field->getId(),
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

    public function testTabPermissionsAction()
    {
        $uri = $this->router->generate('admin_tab_field_permissions', [
            'id' => $this->field->getId(),
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

    public function testTabRegexAction()
    {
        $uri = $this->router->generate('admin_tab_field_regex', [
            'id' => $this->field->getId(),
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

    public function testTabListItemsAction()
    {
        $uri = $this->router->generate('admin_tab_field_listitems', [
            'id' => $this->field->getId(),
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
        $uri = $this->router->generate('admin_dlg_new_field', [
            'id' => $this->state->getId(),
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

    public function testEditAction()
    {
        $uri = $this->router->generate('admin_dlg_edit_field', [
            'id' => $this->field->getId(),
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

    public function testRegexAction()
    {
        $uri = $this->router->generate('admin_dlg_regex_field', [
            'id' => $this->field->getId(),
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

    public function testLoadRolePermissionsAction()
    {
        $uri = $this->router->generate('admin_fields_load_role_permissions', [
            'id'   => $this->field->getId(),
            'role' => SystemRole::AUTHOR,
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

    public function testLoadGroupPermissionsAction()
    {
        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        /** @var Group $group */
        $group = $doctrine->getRepository(Group::class)->findOneBy([
            'name' => 'Crew',
        ]);

        $uri = $this->router->generate('admin_fields_load_group_permissions', [
            'id'    => $this->field->getId(),
            'group' => $group->getId(),
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
