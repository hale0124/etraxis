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

use eTraxis\Entity\Field;
use eTraxis\Entity\ListItem;
use eTraxis\Tests\ControllerTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ListItemsGetControllerTest extends ControllerTestCase
{
    /** @var Field */
    private $field;

    /** @var ListItem */
    private $item;

    protected function setUp()
    {
        parent::setUp();

        /** @var \Symfony\Bridge\Doctrine\RegistryInterface $doctrine */
        $doctrine = $this->client->getContainer()->get('doctrine');

        $this->field = $doctrine->getRepository(Field::class)->findOneBy([
            'name' => 'Season',
        ]);

        $this->item = $doctrine->getRepository(ListItem::class)->findOneBy([
            'key'   => '1',
            'value' => 'Season 1',
        ]);
    }

    public function testNewAction()
    {
        $uri = $this->router->generate('admin_dlg_new_listitem', [
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

    public function testEditAction()
    {
        $uri = $this->router->generate('admin_dlg_edit_listitem', [
            'id'  => $this->field->getId(),
            'key' => $this->item->getKey(),
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
