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

use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Form\GroupExForm;
use eTraxis\Form\GroupForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Groups "GET" controller.
 *
 * @Action\Route("/groups", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class GroupsGetController extends Controller
{
    /**
     * Returns JSON list of groups.
     *
     * @Action\Route("/list/{id}", name="admin_groups_list", requirements={"id"="\d+"})
     *
     * @param   Project $project
     *
     * @return  JsonResponse
     */
    public function listAction(Project $project): JsonResponse
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return new JsonResponse(array_merge($repository->getGlobalGroups(), $project->getGroups()));
    }

    /**
     * Returns JSON list of global groups only.
     *
     * @Action\Route("/list", name="admin_groups_list_global")
     *
     * @return  JsonResponse
     */
    public function listGlobalAction(): JsonResponse
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return new JsonResponse($repository->getGlobalGroups());
    }

    /**
     * Shows specified group.
     *
     * @Action\Route("/{id}", name="admin_view_group", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   Group   $group
     *
     * @return  Response
     */
    public function viewAction(Request $request, Group $group): Response
    {
        return $this->render('admin/groups/view.html.twig', [
            'group' => $group,
            'tab'   => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with group's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_group_details", requirements={"id"="\d+"})
     *
     * @param   Group $group
     *
     * @return  Response
     */
    public function tabDetailsAction(Group $group): Response
    {
        return $this->render('admin/groups/tab_details.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * Tab with group's members.
     *
     * @Action\Route("/tab/groups/{id}", name="admin_tab_group_members", requirements={"id"="\d+"})
     *
     * @param   Group $group
     *
     * @return  Response
     */
    public function tabMembersAction(Group $group): Response
    {
        return $this->render('admin/groups/tab_members.html.twig', [
            'group' => $group,
        ]);
    }

    /**
     * Renders dialog to create new group.
     *
     * @Action\Route("/new/{id}", name="admin_dlg_new_group", requirements={"id"="\d+"})
     *
     * @param   int $id Project ID (NULL for global group creation).
     *
     * @return  Response
     */
    public function newAction(int $id = null): Response
    {
        $class = ($id === null)
            ? GroupForm::class
            : GroupExForm::class;

        $form = $this->createForm($class, ['id' => $id], [
            'action' => $this->generateUrl('admin_new_group'),
        ]);

        $template = ($id === null)
            ? 'admin/groups/dlg_group.html.twig'
            : 'admin/groups/dlg_group_ex.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified group.
     *
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_group", requirements={"id"="\d+"})
     *
     * @param   Group $group
     *
     * @return  Response
     */
    public function editAction(Group $group): Response
    {
        $form = $this->createForm(GroupForm::class, $group, [
            'action' => $this->generateUrl('admin_edit_group', ['id' => $group->getId()]),
        ]);

        return $this->render('admin/groups/dlg_group.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
