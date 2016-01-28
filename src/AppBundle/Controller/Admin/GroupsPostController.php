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
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Groups controller.
 *
 * @Action\Route("/groups")
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
    public function newAction(Request $request)
    {
        try {
            $data = $this->getFormData($request, 'group');

            $command = new Groups\CreateGroupCommand($data);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
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
    public function editAction(Request $request, $id)
    {
        try {
            $group = $this->getDoctrine()->getRepository('eTraxis:Group')->find($id);

            if (!$group) {
                throw $this->createNotFoundException();
            }

            $data = $this->getFormData($request, 'group', ['id' => $id]);

            $command = new Groups\UpdateGroupCommand($data);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Deletes specified group.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_group", requirements={"id"="\d+"})
     *
     * @param   int $id Group ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new Groups\DeleteGroupCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
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
    public function addUsersAction(Request $request, $id)
    {
        try {
            $command = new Groups\AddUsersCommand(
                array_merge(['id' => $id], $request->request->all())
            );

            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
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
    public function removeUsersAction(Request $request, $id)
    {
        try {
            $command = new Groups\RemoveUsersCommand(
                array_merge(['id' => $id], $request->request->all())
            );

            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
    }
}
