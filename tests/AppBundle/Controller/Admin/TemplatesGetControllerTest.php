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
use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplatesGetControllerTest extends ControllerTestCase
{
    /** @var Project */
    private $project;

    /** @var Template */
    private $template;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->project = $doctrine->getRepository(Project::class)->findOneBy([
            'name' => 'Planet Express',
        ]);

        $this->template = $doctrine->getRepository(Template::class)->findOneBy([
            'name' => 'Delivery',
        ]);
    }

    public function testListAction()
    {
        $uri = $this->router->generate('admin_templates_list', [
            'id' => $this->project->getId(),
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
        $uri = $this->router->generate('admin_view_template', [
            'id' => $this->template->getId(),
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
        $uri = $this->router->generate('admin_tab_template_details', [
            'id' => $this->template->getId(),
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
        $uri = $this->router->generate('admin_tab_template_permissions', [
            'id' => $this->template->getId(),
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
        $uri = $this->router->generate('admin_dlg_new_template', [
            'id' => $this->project->getId(),
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
        $uri = $this->router->generate('admin_dlg_edit_template', [
            'id' => $this->template->getId(),
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
        $uri = $this->router->generate('admin_templates_load_role_permissions', [
            'id'   => $this->template->getId(),
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

        $uri = $this->router->generate('admin_templates_load_group_permissions', [
            'id'    => $this->template->getId(),
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
