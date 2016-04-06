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
use eTraxis\SimpleBus\Projects;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $data = $request->request->get('project');

        $command = new Projects\CreateProjectCommand($data);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
        $project = $this->getDoctrine()->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException();
        }

        $data = $request->request->get('project');

        $command = new Projects\UpdateProjectCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
        $command = new Projects\DeleteProjectCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
