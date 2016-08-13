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

use eTraxis\CommandBus\ListItems;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * List items "POST" controller.
 *
 * @Action\Route("/listitems", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class ListItemsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new list item is being created.
     *
     * @Action\Route("/new/{id}", name="admin_new_listitem", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Field ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->get('listitem');

        $command = new ListItems\CreateListItemCommand($data, ['field' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Processes submitted form when specified list item is being edited.
     *
     * @Action\Route("/edit/{id}/{value}", name="admin_edit_listitem", requirements={"id"="\d+", "value"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id    Field ID.
     * @param   int     $value Item value.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, int $id, int $value): JsonResponse
    {
        $data = $request->request->get('listitem');

        $command = new ListItems\UpdateListItemCommand($data, ['field' => $id, 'value' => $value]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Deletes specified list item.
     *
     * @Action\Route("/delete/{id}/{value}", name="admin_delete_listitem", requirements={"id"="\d+", "value"="\d+"})
     *
     * @param   int $id    Field ID.
     * @param   int $value Item value.
     *
     * @return  JsonResponse
     */
    public function deleteAction(int $id, int $value): JsonResponse
    {
        $command = new ListItems\DeleteListItemCommand(['field' => $id, 'value' => $value]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
