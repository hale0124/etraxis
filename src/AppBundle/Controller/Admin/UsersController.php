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

use eTraxis\Form\UserForm;
use eTraxis\SimpleBus\CommandException;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\SimpleBus\Users;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Voter\UserVoter;
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
        $default = [
            'locale' => $this->getParameter('locale'),
            'theme'  => $this->getParameter('theme'),
        ];

        $form = $this->createForm(new UserForm(), $default, [
            'action' => $this->generateUrl('admin_new_user'),
        ]);

        return $this->render('admin/users/index.html.twig', [
            'form' => $form->createView(),
        ]);
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
                'recordsFiltered' => $command->result['filtered'],
                'data'            => $command->result['users'],
            ]);
        }
        catch (ValidationException $e) {
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
        catch (ValidationException $e) {
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

            $authChecker = $this->getAuthorizationChecker();

            return $this->render('admin/users/tab_details.html.twig', [
                'user' => $command->result,
                'form' => $form->createView(),
                'can'  => [
                    'delete'  => $authChecker->isGranted(UserVoter::DELETE, $command->result),
                    'disable' => $authChecker->isGranted(UserVoter::DISABLE, $command->result),
                    'enable'  => $authChecker->isGranted(UserVoter::ENABLE, $command->result),
                    'unlock'  => $authChecker->isGranted(UserVoter::UNLOCK, $command->result),
                ],
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Processes submitted form when new user is being created.
     *
     * @Route("/new", name="admin_new_user")
     * @Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request)
    {
        try {
            $data = $this->getFormData($request, 'user');

            if ($data['password'] != $data['confirmation']) {
                throw new CommandException($this->get('translator')->trans('passwords.dont.match'));
            }

            $command = new Users\CreateUserCommand($data);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (CommandException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Processes submitted form when specified user is being edited.
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
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        try {
            $command = new Users\FindUserCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            if (!$command->result) {
                throw $this->createNotFoundException();
            }

            $em->beginTransaction();

            $data       = $this->getFormData($request, 'user');
            $data['id'] = $id;

            if ($command->result->isLdap()) {
                $data['username'] = $command->result->getUsername();
                $data['fullname'] = $command->result->getFullname();
                $data['email']    = $command->result->getEmail();
            }

            $command = new Users\UpdateUserCommand($data);
            $this->getCommandBus()->handle($command);

            if ($data['password']) {

                if ($data['password'] != $data['confirmation']) {
                    throw new CommandException($this->get('translator')->trans('passwords.dont.match'));
                }

                $command = new Users\SetPasswordCommand([
                    'id'       => $id,
                    'password' => $data['password'],
                ]);

                $this->getCommandBus()->handle($command);
            }

            $em->commit();

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
        catch (CommandException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Deletes specified user.
     *
     * @Route("/{id}/delete", name="admin_delete_user", requirements={"id"="\d+"})
     * @Method("POST")
     *
     * @param   int $id User ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new Users\DeleteUserCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
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
        try {
            $command = new Users\DisableUsersCommand($request->request->all());
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
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
        try {
            $command = new Users\EnableUsersCommand($request->request->all());
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
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
        try {
            $command = new Users\UnlockUserCommand(['id' => $id]);
            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
    }
}
