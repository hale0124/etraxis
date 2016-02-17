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
use eTraxis\Form\GroupExForm;
use eTraxis\Form\GroupForm;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Groups "GET" controller.
 *
 * @Action\Route("/groups", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class GroupsGetController extends Controller
{
    use ContainerTrait;

    /**
     * Returns JSON list of groups.
     *
     * @Action\Route("/list/{id}", name="admin_groups_list", requirements={"id"="\d+"})
     *
     * @param   int $id Project ID.
     *
     * @return  Response|JsonResponse
     */
    public function listAction($id = 0)
    {
        try {
            /** @var \eTraxis\Repository\GroupsRepository $repository */
            $repository = $this->getDoctrine()->getRepository(Group::class);

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
     *
     * @param   Request $request
     * @param   int     $id Group ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id = 0)
    {
        try {
            $group = $this->getDoctrine()->getRepository(Group::class)->find($id);

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
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $group = $this->getDoctrine()->getRepository(Group::class)->find($id);

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
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function tabMembersAction($id)
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

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
     * @Action\Route("/new/{id}", requirements={"id"="\d+"})
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function newAction($id = 0)
    {
        $class = $id
            ? GroupExForm::class
            : GroupForm::class;

        $form = $this->createForm($class, ['id' => $id], [
            'action' => $this->generateUrl('admin_new_group'),
        ]);

        $template = $id
            ? 'admin/groups/dlg_group_ex.html.twig'
            : 'admin/groups/dlg_group.html.twig';

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified group.
     *
     * @Action\Route("/edit/{id}", requirements={"id"="\d+"})
     *
     * @param   int $id Group ID.
     *
     * @return  Response
     */
    public function editAction($id)
    {
        try {
            $group = $this->getDoctrine()->getRepository(Group::class)->find($id);

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
}
