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

use eTraxis\Entity\Project;
use eTraxis\Form\ProjectForm;
use eTraxis\Service\ExportCsvQuery;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\Projects;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Returns JSON list of projects for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Action\Route("/list", name="admin_projects_list")
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
            $results    = $datatables->handle($request, 'eTraxis:Project');

            return new JsonResponse($results);
        }
        catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Exports list of projects as CSV file.
     *
     * @Action\Route("/csv", name="admin_projects_csv")
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
            $results    = $datatables->handle($request, 'eTraxis:Project');

            $projects = array_map(function ($project) {
                return array_slice($project, 0, 3);
            }, $results['data']);

            array_unshift($projects, [
                $this->getTranslator()->trans('project.name'),
                $this->getTranslator()->trans('project.start_time'),
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

            return $export->exportCsv($query, $projects);
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
    public function viewAction(Request $request, $id)
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
        catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
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
        catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
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
        catch (\Exception $e) {
            return new Response($e->getMessage(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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

            $data       = $this->getFormData($request, 'project');
            $data['id'] = $id;

            $command = new Projects\UpdateProjectCommand($data);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
