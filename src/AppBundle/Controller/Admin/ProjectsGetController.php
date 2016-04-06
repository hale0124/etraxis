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
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Projects "GET" controller.
 *
 * @Action\Route("/projects", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class ProjectsGetController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of projects.
     *
     * @Action\Route("/", name="admin_projects", condition="")
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
     *
     * @return  JsonResponse
     */
    public function listAction()
    {
        /** @var \eTraxis\Repository\ProjectsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Project::class);

        return new JsonResponse($repository->getProjects());
    }

    /**
     * Shows specified project.
     *
     * @Action\Route("/{id}", name="admin_view_project", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/projects/view.html.twig', [
            'project' => $project,
            'tab'     => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with project's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_project_details", requirements={"id"="\d+"})
     *
     * @param   int $id Project ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

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

    /**
     * Renders dialog to create new project.
     *
     * @Action\Route("/new", name="admin_dlg_new_project")
     *
     * @return  Response
     */
    public function newAction()
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
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_project", requirements={"id"="\d+"})
     *
     * @param   int     $id Project ID.
     *
     * @return  Response
     */
    public function editAction($id)
    {
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

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
}
