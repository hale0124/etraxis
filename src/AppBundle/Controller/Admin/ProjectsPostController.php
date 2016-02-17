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
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\Projects;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Projects "POST" controller.
 *
 * @Action\Route("/projects", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class ProjectsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new project is being created.
     *
     * @Action\Route("/new", name="admin_new_project")
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
     *
     * @param   Request $request
     * @param   int     $id Project ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

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
