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
use eTraxis\Entity\Template;
use eTraxis\Form\TemplateForm;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\Templates;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Templates controller.
 *
 * @Action\Route("/templates")
 */
class TemplatesController extends Controller
{
    use ContainerTrait;

    /**
     * Returns JSON list of templates.
     *
     * @Action\Route("/list/{id}", name="admin_templates_list", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Project ID.
     *
     * @return  Response|JsonResponse
     */
    public function listAction($id = 0)
    {
        try {
            /** @var \eTraxis\Repository\TemplatesRepository $repository */
            $repository = $this->getDoctrine()->getRepository('eTraxis:Template');

            return new JsonResponse($repository->getTemplates($id));
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Shows specified template.
     *
     * @Action\Route("/{id}", name="admin_view_template", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   Request $request
     * @param   int     $id Template ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id = 0)
    {
        try {
            $template = $this->getDoctrine()->getRepository('eTraxis:Template')->find($id);

            if (!$template) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/templates/view.html.twig', [
                'template' => $template,
                'tab'      => $request->get('tab', 0),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with template's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_template_details", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Template ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $template = $this->getDoctrine()->getRepository('eTraxis:Template')->find($id);

            if (!$template) {
                throw $this->createNotFoundException();
            }

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
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with template's permissions.
     *
     * @Action\Route("/tab/permissions/{id}", name="admin_tab_template_permissions", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Template ID.
     *
     * @return  Response
     */
    public function tabPermissionsAction($id)
    {
        /** @var Template $template */
        $template = $this->getDoctrine()->getRepository('eTraxis:Template')->find($id);

        if (!$this) {
            throw $this->createNotFoundException();
        }

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
        $repository = $this->getDoctrine()->getRepository('eTraxis:Group');

        return $this->render('admin/templates/tab_permissions.html.twig', [
            'template'    => $template,
            'locals'      => $repository->getLocalGroups($template->getProjectId()),
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
     * @Action\Route("/dlg/new/{id}", name="admin_dlg_new_template", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function dlgNewAction($id = 0)
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
     * @Action\Route("/dlg/edit/{id}", name="admin_dlg_edit_template", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Template ID.
     *
     * @return  Response
     */
    public function dlgEditAction($id)
    {
        try {
            $template = $this->getDoctrine()->getRepository('eTraxis:Template')->find($id);

            if (!$template) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(TemplateForm::class, $template, [
                'action' => $this->generateUrl('admin_edit_template', ['id' => $id]),
            ]);

            return $this->render('admin/templates/dlg_template.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Processes submitted form when new template is being created.
     *
     * @Action\Route("/new/{id}", name="admin_new_template", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, $id)
    {
        try {
            $data = $this->getFormData($request, 'template', ['project' => $id]);

            $command = new Templates\CreateTemplateCommand($data);
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
     * Processes submitted form when specified template is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_template", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id Template ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            $template = $this->getDoctrine()->getRepository('eTraxis:Template')->find($id);

            if (!$template) {
                throw $this->createNotFoundException();
            }

            $data = $this->getFormData($request, 'template', ['id' => $id]);

            $command = new Templates\UpdateTemplateCommand($data);
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
     * Deletes specified template.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_template", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new Templates\DeleteTemplateCommand(['id' => $id]);
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
     * Locks specified template.
     *
     * @Action\Route("/lock/{id}", name="admin_lock_template", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function lockAction($id)
    {
        try {
            $command = new Templates\LockTemplateCommand(['id' => $id]);
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
     * Unlocks specified template.
     *
     * @Action\Route("/unlock/{id}", name="admin_unlock_template", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function unlockAction($id)
    {
        try {
            $command = new Templates\UnlockTemplateCommand(['id' => $id]);
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
     * Loads permissions of the specified template.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_load_template_permissions", requirements={"id"="\d+", "group"="[\-]?\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id    Template ID.
     * @param   int $group Group ID or system role.
     *
     * @return  JsonResponse
     */
    public function loadPermissionsAction($id, $group = null)
    {
        try {
            /** @var \eTraxis\Repository\TemplatesRepository $repository */
            $repository = $this->getDoctrine()->getRepository('eTraxis:Template');

            /** @var Template $template */
            $template = $repository->find($id);

            if (!$template) {
                throw $this->createNotFoundException();
            }

            switch ($group) {

                case SystemRole::AUTHOR:
                    $permissions = $template->getAuthorPermissions();
                    $permissions |= Template::PERMIT_VIEW_RECORD;
                    $permissions &= ~Template::PERMIT_CREATE_RECORD;
                    break;

                case SystemRole::RESPONSIBLE:
                    $permissions = $template->getResponsiblePermissions();
                    $permissions |= Template::PERMIT_VIEW_RECORD;
                    $permissions &= ~Template::PERMIT_CREATE_RECORD;
                    break;

                case SystemRole::REGISTERED:
                    $permissions = $template->getRegisteredPermissions();
                    break;

                default:
                    $permissions = $repository->getPermissions($id, $group);
            }

            return new JsonResponse($permissions);
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Saves permissions of the specified template.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_save_template_permissions", requirements={"id"="\d+", "group"="[\-]?\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id    Template ID.
     * @param   int     $group Group ID or system role.
     *
     * @return  JsonResponse
     */
    public function savePermissionsAction(Request $request, $id, $group = null)
    {
        try {
            $command = new Templates\RemoveTemplatePermissionsCommand([
                'id'          => $id,
                'group'       => $group,
                'permissions' => PHP_INT_MAX,
            ]);

            $this->getCommandBus()->handle($command);

            $command = new Templates\AddTemplatePermissionsCommand([
                'id'          => $id,
                'group'       => $group,
                'permissions' => intval($request->request->get('permissions')),
            ]);

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
