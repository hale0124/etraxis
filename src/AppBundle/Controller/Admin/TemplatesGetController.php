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

use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\Form\TemplateForm;
use eTraxis\Traits\ContainerTrait;
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
    use ContainerTrait;

    /**
     * Returns JSON list of templates.
     *
     * @Action\Route("/list/{id}", name="admin_templates_list", requirements={"id"="\d+"})
     *
     * @param   Project $project
     *
     * @return  JsonResponse
     */
    public function listAction(Project $project)
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
    public function viewAction(Request $request, Template $template)
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
    public function tabDetailsAction(Template $template)
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
    public function tabPermissionsAction(Template $template)
    {
        $permissions = [
            'template.permission.view_records'      => Template::PERMIT_VIEW_RECORD,
            'template.permission.create_records'    => Template::PERMIT_CREATE_RECORD,
            'template.permission.edit_records'      => Template::PERMIT_EDIT_RECORD,
            'template.permission.postpone_records'  => Template::PERMIT_POSTPONE_RECORD,
            'template.permission.resume_records'    => Template::PERMIT_RESUME_RECORD,
            'template.permission.reassign_records'  => Template::PERMIT_REASSIGN_RECORD,
            'template.permission.reopen_records'    => Template::PERMIT_REOPEN_RECORD,
            'template.permission.add_comments'      => Template::PERMIT_ADD_COMMENT,
            'template.permission.add_files'         => Template::PERMIT_ADD_FILE,
            'template.permission.remove_files'      => Template::PERMIT_REMOVE_FILE,
            'template.permission.private_comments'  => Template::PERMIT_PRIVATE_COMMENT,
            'template.permission.send_reminders'    => Template::PERMIT_SEND_REMINDER,
            'template.permission.delete_records'    => Template::PERMIT_DELETE_RECORD,
            'template.permission.attach_subrecords' => Template::PERMIT_ATTACH_SUBRECORD,
            'template.permission.detach_subrecords' => Template::PERMIT_DETACH_SUBRECORD,
        ];

        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return $this->render('admin/templates/tab_permissions.html.twig', [
            'template'    => $template,
            'locals'      => $template->getProject()->getGroups(),
            'globals'     => $repository->getGlobalGroups(),
            'permissions' => $permissions,
            'role'        => [
                'author'      => SystemRole::AUTHOR,
                'responsible' => SystemRole::RESPONSIBLE,
                'registered'  => SystemRole::REGISTERED,
            ],
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
    public function newAction($id)
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
    public function editAction(Template $template)
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
     * @Action\Route("/permissions/{id}/{role}", name="admin_load_template_permissions_role", requirements={"id"="\d+", "role"="[\-]\d+"})
     *
     * @param   Template $template
     * @param   int      $role
     *
     * @return  JsonResponse
     */
    public function loadRolePermissionsAction(Template $template, $role)
    {
        /** @var \eTraxis\Repository\TemplatesRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Template::class);

        return new JsonResponse($repository->getRolePermissions($template, $role));
    }

    /**
     * Loads permissions of the specified group for the specified template.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_load_template_permissions", requirements={"id"="\d+", "group"="\d+"})
     *
     * @param   Template $template
     * @param   Group     $group
     *
     * @return  JsonResponse
     */
    public function loadGroupPermissionsAction(Template $template, Group $group)
    {
        /** @var \eTraxis\Repository\TemplatesRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Template::class);

        return new JsonResponse($repository->getGroupPermissions($template, $group));
    }
}
