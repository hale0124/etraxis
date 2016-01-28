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

use eTraxis\Entity\Project;
use eTraxis\Form\ProjectForm;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\Projects;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Projects controller.
 *
 * @Action\Route("/projects")
 */
class ProjectsController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of projects.
     *
     * @Action\Route("/", name="admin_projects")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function indexAction()
    {
        return $this->render('admin/projects/index.html.twig');
    }

    /**
     * Returns JSON list of projects.
     *
     * @Action\Route("/list", name="admin_projects_list")
     * @Action\Method("GET")
     *
     * @return  Response|JsonResponse
     */
    public function listAction()
    {
        try {
            /** @var \eTraxis\Repository\ProjectsRepository $repository */
            $repository = $this->getDoctrine()->getRepository('eTraxis:Project');

            return new JsonResponse($repository->getProjects());
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Shows specified project.
     *
     * @Action\Route("/{id}", name="admin_view_project", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id = 0)
    {
        try {
            $project = $this->getDoctrine()->getRepository('eTraxis:Project')->find($id);

            if (!$project) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/projects/view.html.twig', [
                'project' => $project,
                'tab'     => $request->get('tab', 0),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with project's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_project_details", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $project = $this->getDoctrine()->getRepository('eTraxis:Project')->find($id);

            if (!$project) {
                throw $this->createNotFoundException();
            }

            /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
            $authChecker = $this->get('security.authorization_checker');

            return $this->render('admin/projects/tab_details.html.twig', [
                'project' => $project,
                'can'     => [
                    'delete' => $authChecker->isGranted(Project::DELETE, $project),
                ],
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Renders dialog to create new project.
     *
     * @Action\Route("/dlg/new", name="admin_dlg_new_project")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function dlgNewAction()
    {
        $default = [
            'suspended' => true,
        ];

        $form = $this->createForm(ProjectForm::class, $default, [
            'action' => $this->generateUrl('admin_new_project'),
        ]);

        return $this->render('admin/projects/dlg_project.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified project.
     *
     * @Action\Route("/dlg/edit/{id}", name="admin_dlg_edit_project", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int     $id Project ID.
     *
     * @return  Response
     */
    public function dlgEditAction($id)
    {
        try {
            $project = $this->getDoctrine()->getRepository('eTraxis:Project')->find($id);

            if (!$project) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(ProjectForm::class, $project, [
                'action' => $this->generateUrl('admin_edit_project', ['id' => $id]),
            ]);

            return $this->render('admin/projects/dlg_project.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Processes submitted form when new project is being created.
     *
     * @Action\Route("/new", name="admin_new_project")
     * @Action\Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request)
    {
        try {
            $data = $this->getFormData($request, 'project');

            $command = new Projects\CreateProjectCommand($data);
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
     * Processes submitted form when specified project is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_project", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            $project = $this->getDoctrine()->getRepository('eTraxis:Project')->find($id);

            if (!$project) {
                throw $this->createNotFoundException();
            }

            $data = $this->getFormData($request, 'project', ['id' => $id]);

            $command = new Projects\UpdateProjectCommand($data);
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
     * Deletes specified project.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_project", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   int $id Project ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new Projects\DeleteProjectCommand(['id' => $id]);
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
