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

use eTraxis\SimpleBus\Groups;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Groups "POST" controller.
 *
 * @Action\Route("/groups", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class GroupsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new group is being created.
     *
     * @Action\Route("/new", name="admin_new_group")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request): JsonResponse
    {
        $data = $request->request->get('group');

        $command = new Groups\CreateGroupCommand($data);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Processes submitted form when specified group is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_group", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->get('group');

        $command = new Groups\UpdateGroupCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Deletes specified group.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_group", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        $command = new Groups\DeleteGroupCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Adds specified users to the group.
     *
     * @Action\Route("/users/add/{id}", name="admin_groups_add_users", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  JsonResponse
     */
    public function addUsersAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        $command = new Groups\AddUsersCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Removes specified users from the group.
     *
     * @Action\Route("/users/remove/{id}", name="admin_groups_remove_users", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  JsonResponse
     */
    public function removeUsersAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        $command = new Groups\RemoveUsersCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
