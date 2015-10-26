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

use eTraxis\DataTables\DataTableException;
use eTraxis\Form\GroupExForm;
use eTraxis\Form\GroupForm;
use eTraxis\Service\ExportCsvQuery;
use eTraxis\SimpleBus\CommandException;
use eTraxis\SimpleBus\Groups;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Groups controller.
 *
 * @Action\Route("/groups")
 */
class GroupsController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of groups.
     *
     * @Action\Route("/", name="admin_groups")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function indexAction()
    {
        return $this->render('admin/groups/index.html.twig', [
            'projects' => $this->getDoctrine()->getRepository('eTraxis:Project')->findAll(),
        ]);
    }

    /**
     * Returns JSON list of groups for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Action\Route("/list", name="admin_groups_list")
     * @Action\Method("GET")
     *
     * @param   Request $request
     *
     * @return  Response|JsonResponse
     */
    public function listAction(Request $request)
    {
        try {
            $datatables = $this->getDataTables();
            $results    = $datatables->handle($request, 'eTraxis:Group');

            return new JsonResponse($results);
        }
        catch (DataTableException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Exports list of groups as CSV file.
     *
     * @Action\Route("/csv", name="admin_groups_csv")
     * @Action\Method("GET")
     *
     * @param   Request $request
     *
     * @return  StreamedResponse|JsonResponse
     */
    public function csvAction(Request $request)
    {
        try {
            $request->query->set('start', 0);
            $request->query->set('length', -1);

            $datatables = $this->getDataTables();
            $results    = $datatables->handle($request, 'eTraxis:Group');

            $groups = array_map(function ($group) {
                return array_slice($group, 0, 4);
            }, $results['data']);

            array_unshift($groups, [
                $this->getTranslator()->trans('group.name'),
                $this->getTranslator()->trans('group.type'),
                $this->getTranslator()->trans('project'),
                $this->getTranslator()->trans('description'),
            ]);

            $query = new ExportCsvQuery($this->getFormData($request, 'export'));

            /** @var \Symfony\Component\Validator\ConstraintViolationInterface[] $violations */
            $violations = $this->get('validator')->validate($query);

            if (count($violations)) {
                throw new ValidationException($violations);
            }

            /** @var \eTraxis\Service\ExportInterface $export */
            $export = $this->get('etraxis.export');

            return $export->exportCsv($query, $groups);
        }
        catch (DataTableException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
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
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
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
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
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
     * @Action\Route("/dlg/new", name="admin_dlg_new_group")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function dlgNewAction()
    {
        $form = $this->createForm(new GroupExForm($this->getTranslator()), null, [
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
     * @param   int     $id Group ID.
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

            $form = $this->createForm(new GroupForm(), $group, [
                'action' => $this->generateUrl('admin_edit_group', ['id' => $id]),
            ]);

            return $this->render('admin/groups/dlg_group.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (CommandException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (CommandException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
    }
}
