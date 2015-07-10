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

use eTraxis\Exception\ResponseException;
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
     * @return  JsonResponse
     */
    public function ajaxAction(Request $request)
    {
        $search = $request->get('search', ['value' => null]);

        $command = new Users\ListUsersCommand([
            'start'  => $request->get('start', 0),
            'length' => $request->get('length', -1),
            'search' => $search['value'],
            'order'  => $request->get('order', []),
        ]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        return new JsonResponse([
            'draw'            => $request->get('draw'),
            'recordsTotal'    => $command->total,
            'recordsFiltered' => $command->total,
            'data'            => $command->users,
        ]);
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
        $command = new Users\FindUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        if (!$command->user) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/users/view.html.twig', [
            'user' => $command->user,
            'tab'  => $request->get('tab', 0),
        ]);
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
        $command = new Users\FindUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        if (!$command->user) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/users/tab_details.html.twig', [
            'user' => $command->user,
        ]);
    }

    /**
     * Disables specified user.
     *
     * @Route("/{id}/disable", name="admin_disable_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function disableAction($id)
    {
        // Don't disable yourself.
        if ($this->getUser()->getId() == $id) {
            return new Response();
        }

        $command = new Users\DisableUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        return new Response();
    }

    /**
     * Enables specified user.
     *
     * @Route("/{id}/enable", name="admin_enable_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function enableAction($id)
    {
        $command = new Users\EnableUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        return new Response();
    }

    /**
     * Unlocks specified user.
     *
     * @Route("/{id}/unlock", name="admin_unlock_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function unlockAction($id)
    {
        $command = new Users\FindUserCommand(['id' => $id]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        if (!$command->user) {
            throw $this->createNotFoundException();
        }

        $command = new Users\UnlockUserCommand(['username' => $command->user->getUsername()]);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }

        return new Response();
    }
}
