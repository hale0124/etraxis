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

use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\States;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * States "POST" controller.
 *
 * @Action\Route("/states")
 * @Action\Method("POST")
 */
class StatesPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new state is being created.
     *
     * @Action\Route("/new/{id}", name="admin_new_state", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Template ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, $id = 0)
    {
        try {
            $data = $this->getFormData($request, 'state', ['template' => $id]);

            $command = new States\CreateStateCommand($data);
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
     * Processes submitted form when specified state is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_state", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id State ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            $state = $this->getDoctrine()->getRepository('eTraxis:State')->find($id);

            if (!$state) {
                throw $this->createNotFoundException();
            }

            $data = $this->getFormData($request, 'state', ['id' => $id]);

            $command = new States\UpdateStateCommand($data);
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
     * Deletes specified state.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_state", requirements={"id"="\d+"})
     *
     * @param   int $id State ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new States\DeleteStateCommand(['id' => $id]);
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
     * Sets specified state as initial.
     *
     * @Action\Route("/lock/{id}", name="admin_initial_state", requirements={"id"="\d+"})
     *
     * @param   int $id State ID.
     *
     * @return  JsonResponse
     */
    public function initialAction($id)
    {
        try {
            $command = new States\SetInitialStateCommand(['id' => $id]);
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