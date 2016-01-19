<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use eTraxis\Form\GroupExForm;
use eTraxis\Form\GroupForm;
use eTraxis\SimpleBus\Groups;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Groups controller.
 *
 * @Action\Route("/groups")
 */
class GroupsController extends Controller
{
    use ContainerTrait;

    /**
     * Returns JSON list of groups.
     *
     * @Action\Route("/list/{id}", name="admin_groups_list", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Project ID.
     *
     * @return  Response|JsonResponse
     */
    public function listAction($id)
    {
        try {
            /** @var \eTraxis\Repository\GroupsRepository $repository */
            $repository = $this->getDoctrine()->getRepository('eTraxis:Group');

            return new JsonResponse($repository->getGroups($id));
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Shows specified group.
     *
     * @Action\Route("/{id}", name="admin_view_group", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id)
    {
        try {
            $group = $this->getDoctrine()->getRepository('eTraxis:Group')->find($id);

            if (!$group) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/groups/view.html.twig', [
                'group' => $group,
                'tab'   => $request->get('tab', 0),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with group's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_group_details", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $group = $this->getDoctrine()->getRepository('eTraxis:Group')->find($id);

            if (!$group) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/groups/tab_details.html.twig', [
                'group' => $group,
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with group's members.
     *
     * @Action\Route("/tab/groups/{id}", name="admin_tab_group_members", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function tabMembersAction($id)
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository('eTraxis:Group');

        $group = $repository->find($id);

        if (!$group) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/groups/tab_members.html.twig', [
            'group'   => $group,
            'members' => $repository->getGroupMembers($id),
            'others'  => $repository->getGroupNonMembers($id),
        ]);
    }

    /**
     * Renders dialog to create new group.
     *
     * @Action\Route("/dlg/new/{id}", name="admin_dlg_new_group", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function dlgNewAction($id = null)
    {
        $form = $this->createForm(GroupExForm::class, ['id' => $id], [
            'action' => $this->generateUrl('admin_new_group'),
        ]);

        return $this->render('admin/groups/dlg_group_ex.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified group.
     *
     * @Action\Route("/dlg/edit/{id}", name="admin_dlg_edit_group", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function dlgEditAction($id)
    {
        try {
            $group = $this->getDoctrine()->getRepository('eTraxis:Group')->find($id);

            if (!$group) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(GroupForm::class, $group, [
                'action' => $this->generateUrl('admin_edit_group', ['id' => $id]),
            ]);

            return $this->render('admin/groups/dlg_group.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Processes submitted form when new group is being created.
     *
     * @Action\Route("/new", name="admin_new_group")
     * @Action\Method("POST")
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
     * @Action\Method("POST")
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

            $data       = $this->getFormData($request, 'group');
            $data['id'] = $id;

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
     * @Action\Method("POST")
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
     * @Action\Method("POST")
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
     * @Action\Method("POST")
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
