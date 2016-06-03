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
use eTraxis\Dictionary\TemplatePermission;
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\Form\TemplateForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Templates "GET" controller.
 *
 * @Action\Route("/templates", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class TemplatesGetController extends Controller
{
    /**
     * Returns JSON list of templates.
     *
     * @Action\Route("/list/{id}", name="admin_templates_list", requirements={"id"="\d+"})
     *
     * @param   Project $project
     *
     * @return  JsonResponse
     */
    public function listAction(Project $project): JsonResponse
    {
        return new JsonResponse($project->getTemplates());
    }

    /**
     * Shows specified template.
     *
     * @Action\Route("/{id}", name="admin_view_template", requirements={"id"="\d+"})
     *
     * @param   Request  $request
     * @param   Template $template
     *
     * @return  Response
     */
    public function viewAction(Request $request, Template $template): Response
    {
        return $this->render('admin/templates/view.html.twig', [
            'template' => $template,
            'tab'      => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with template's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_template_details", requirements={"id"="\d+"})
     *
     * @param   Template $template
     *
     * @return  Response
     */
    public function tabDetailsAction(Template $template): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        return $this->render('admin/templates/tab_details.html.twig', [
            'template' => $template,
            'can'      => [
                'delete' => $authChecker->isGranted(Template::DELETE, $template),
                'lock'   => $authChecker->isGranted(Template::LOCK, $template),
                'unlock' => $authChecker->isGranted(Template::UNLOCK, $template),
            ],
        ]);
    }

    /**
     * Tab with template's permissions.
     *
     * @Action\Route("/tab/permissions/{id}", name="admin_tab_template_permissions", requirements={"id"="\d+"})
     *
     * @param   Template $template
     *
     * @return  Response
     */
    public function tabPermissionsAction(Template $template): Response
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return $this->render('admin/templates/tab_permissions.html.twig', [
            'template'    => $template,
            'groups'      => $repository->getGlobalGroups(),
            'roles'       => SystemRole::all(),
            'permissions' => TemplatePermission::all(),
        ]);
    }

    /**
     * Renders dialog to create new template.
     *
     * @Action\Route("/new/{id}", name="admin_dlg_new_template", requirements={"id"="\d+"})
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function newAction(int $id): Response
    {
        $form = $this->createForm(TemplateForm::class, null, [
            'action' => $this->generateUrl('admin_new_template', ['id' => $id]),
        ]);

        return $this->render('admin/templates/dlg_template.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified template.
     *
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_template", requirements={"id"="\d+"})
     *
     * @param   Template $template
     *
     * @return  Response
     */
    public function editAction(Template $template): Response
    {
        $form = $this->createForm(TemplateForm::class, $template, [
            'action' => $this->generateUrl('admin_edit_template', ['id' => $template->getId()]),
        ]);

        return $this->render('admin/templates/dlg_template.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Loads permissions of the specified role for the specified template.
     *
     * @Action\Route("/permissions/{id}/{role}", name="admin_templates_load_role_permissions", requirements={"id"="\d+", "role"="\D+"})
     *
     * @param   Template $template
     * @param   string   $role
     *
     * @return  JsonResponse
     */
    public function loadRolePermissionsAction(Template $template, string $role): JsonResponse
    {
        return new JsonResponse($template->getRolePermissions($role));
    }

    /**
     * Loads permissions of the specified group for the specified template.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_templates_load_group_permissions", requirements={"id"="\d+", "group"="\d+"})
     *
     * @param   Template $template
     * @param   Group    $group
     *
     * @return  JsonResponse
     */
    public function loadGroupPermissionsAction(Template $template, Group $group): JsonResponse
    {
        return new JsonResponse($template->getGroupPermissions($group));
    }
}
