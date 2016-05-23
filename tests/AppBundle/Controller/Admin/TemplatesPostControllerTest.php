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
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplatesPostControllerTest extends ControllerTestCase
{
    /** @var Project */
    private $project;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->project = $doctrine->getRepository(Project::class)->findOneBy([
            'name' => 'Planet Express',
        ]);
    }

    public function testNewAction()
    {
        $uri = $this->router->generate('admin_new_template', [
            'id' => $this->project->getId(),
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
        $uri = $this->router->generate('admin_edit_template', [
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
        $uri = $this->router->generate('admin_delete_template', [
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

    public function testLockAction()
    {
        $uri = $this->router->generate('admin_lock_template', [
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

    public function testUnlockAction()
    {
        $uri = $this->router->generate('admin_unlock_template', [
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

    public function testSaveRolePermissionsAction()
    {
        $uri = $this->router->generate('admin_templates_save_role_permissions', [
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

        $uri = $this->router->generate('admin_templates_save_group_permissions', [
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
