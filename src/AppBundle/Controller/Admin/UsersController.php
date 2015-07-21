<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use eTraxis\Exception\CommandException;
use eTraxis\Exception\ResponseException;
use eTraxis\Form\UserForm;
use eTraxis\SimpleBus\Users;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Users controller.
 *
 * @Route("/users")
 */
class UsersController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of users.
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/users/index.html.twig');
    }

    /**
     * Returns JSON list of users for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Route("/ajax", name="admin_users_ajax")
     * @Method("GET")
     *
     * @param   Request $request
     *
     * @return  Response|JsonResponse
     */
    public function ajaxAction(Request $request)
    {
        try {
            $search = $request->get('search', ['value' => null]);

            $command = new Users\ListUsersCommand([
                'start'  => $request->get('start', 0),
                'length' => $request->get('length', -1),
                'search' => $search['value'],
                'order'  => $request->get('order', []),
            ]);

            $this->getCommandBus()->handle($command);

            return new JsonResponse([
                'draw'            => $request->get('draw'),
                'recordsTotal'    => $command->result['total'],
                'recordsFiltered' => $command->result['total'],
                'data'            => $command->result['users'],
            ]);
        }
        catch (CommandException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Shows specified user.
     *
     * @Route("/{id}", name="admin_view_user", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id)
    {
        try {
            $command = new Users\FindUserCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            if (!$command->result) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/users/view.html.twig', [
                'user' => $command->result,
                'tab'  => $request->get('tab', 0),
            ]);
        }
        catch (CommandException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Tab with user's details.
     *
     * @Route("/{id}/tab/details", name="admin_tab_user_details", requirements={"id"="\d+"})
     * @Method("GET")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $command = new Users\FindUserCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            if (!$command->result) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(new UserForm(), $command->result, [
                'action' => $this->generateUrl('admin_edit_user', ['id' => $id]),
            ]);

            return $this->render('admin/users/tab_details.html.twig', [
                'user' => $command->result,
                'form' => $form->createView(),
            ]);
        }
        catch (CommandException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Processes submitted form when specified user is edited.
     *
     * @Route("/{id}/edit", name="admin_edit_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            $data       = $this->getFormData($request, 'user');
            $data['id'] = $id;

            $command = new Users\UpdateUserCommand($data);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (CommandException $e) {
            return new JsonResponse($command->errors, $e->getCode());
        }
        catch (ResponseException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Disables specified users.
     *
     * @Route("/disable", name="admin_disable_user")
     * @Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function disableAction(Request $request)
    {
        $command = new Users\DisableUsersCommand($request->request->all());

        try {
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (CommandException $e) {
            return new JsonResponse($command->errors, $e->getCode());
        }
    }

    /**
     * Enables specified users.
     *
     * @Route("/enable", name="admin_enable_user")
     * @Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function enableAction(Request $request)
    {
        $command = new Users\EnableUsersCommand($request->request->all());

        try {
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (CommandException $e) {
            return new JsonResponse($command->errors, $e->getCode());
        }
    }

    /**
     * Unlocks specified user.
     *
     * @Route("/{id}/unlock", name="admin_unlock_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   int $id User ID.
     *
     * @return  JsonResponse
     */
    public function unlockAction($id)
    {
        $command = new Users\UnlockUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (CommandException $e) {
            return new JsonResponse($command->errors, $e->getCode());
        }
    }
}
