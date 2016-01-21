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
                ],
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
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
            $data            = $this->getFormData($request, 'template');
            $data['project'] = $id;

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

            $data       = $this->getFormData($request, 'template');
            $data['id'] = $id;

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
}
