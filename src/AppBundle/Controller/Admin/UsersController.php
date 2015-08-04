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

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\LineEnding;
use eTraxis\CommandBus\CommandException;
use eTraxis\CommandBus\Shared\ExportToCsvCommand;
use eTraxis\CommandBus\Users;
use eTraxis\CommandBus\ValidationException;
use eTraxis\Form\ExportCsvForm;
use eTraxis\Form\UserForm;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Voter\UserVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Users controller.
 *
 * @Action\Route("/users")
 */
class UsersController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of users.
     *
     * @Action\Route("/", name="admin_users")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function indexAction()
    {
        return $this->render('admin/users/index.html.twig');
    }

    /**
     * Returns JSON list of users for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Action\Route("/list", name="admin_users_list")
     * @Action\Method("GET")
     *
     * @param   Request $request
     *
     * @return  Response|JsonResponse
     */
    public function listAction(Request $request)
    {
        try {
            $search = $request->get('search', ['value' => null]);

            $command = new Users\ListUsersCommand([
                'start'  => $request->get('start', 0),
                'length' => $request->get('length', -1),
                'search' => $search['value'],
                'order'  => $request->get('order', []),
            ]);

            $result = $this->getCommandBus()->handle($command);

            return new JsonResponse([
                'draw'            => $request->get('draw'),
                'recordsTotal'    => $result['total'],
                'recordsFiltered' => $result['filtered'],
                'data'            => $result['users'],
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Exports list of users as CSV file.
     *
     * @Action\Route("/csv", name="admin_users_csv")
     * @Action\Method("GET")
     *
     * @param   Request $request
     *
     * @return  StreamedResponse|JsonResponse
     */
    public function csvAction(Request $request)
    {
        try {
            $command = new Users\ListUsersCommand([
                'search' => $request->get('search'),
            ]);

            $result = $this->getCommandBus()->handle($command);

            $users = array_map(function ($user) {
                return array_slice($user, 1, 6);
            }, $result['users']);

            array_unshift($users, [
                $this->getTranslator()->trans('user.username'),
                $this->getTranslator()->trans('user.fullname'),
                $this->getTranslator()->trans('user.email'),
                $this->getTranslator()->trans('permissions'),
                $this->getTranslator()->trans('security.authentication'),
                $this->getTranslator()->trans('description'),
            ]);

            $command = new ExportToCsvCommand($this->getFormData($request, 'export'));

            $command->data = $users;

            return $this->getCommandBus()->handle($command);
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
    }

    /**
     * Shows specified user.
     *
     * @Action\Route("/{id}", name="admin_view_user", requirements={"id"="\d+"})
     * @Action\Method("GET")
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

            $user = $this->getCommandBus()->handle($command);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/users/view.html.twig', [
                'user' => $user,
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
     * @Action\Route("/tab/details/{id}", name="admin_tab_user_details", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            $command = new Users\FindUserCommand(['id' => $id]);

            $user = $this->getCommandBus()->handle($command);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $authChecker = $this->getAuthorizationChecker();

            return $this->render('admin/users/tab_details.html.twig', [
                'user' => $user,
                'can'  => [
                    'delete'  => $authChecker->isGranted(UserVoter::DELETE, $user),
                    'disable' => $authChecker->isGranted(UserVoter::DISABLE, $user),
                    'enable'  => $authChecker->isGranted(UserVoter::ENABLE, $user),
                    'unlock'  => $authChecker->isGranted(UserVoter::UNLOCK, $user),
                ],
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Renders dialog to export users to CSV.
     *
     * @Action\Route("/dlg/export", name="admin_dlg_export")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function dlgExportAction()
    {
        $default = [
            'filename'  => '.csv',
            'delimiter' => CsvDelimiter::COMMA,
            'encoding'  => 'UTF-8',
            'tail'      => LineEnding::WINDOWS,
        ];

        $form = $this->createForm(new ExportCsvForm($this->getTranslator()), $default, [
            'action' => $this->generateUrl('admin_users_export'),
        ]);

        return $this->render('shared/dlg_export.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to create new user.
     *
     * @Action\Route("/dlg/new", name="admin_dlg_new_user")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function dlgNewAction()
    {
        $default = [
            'locale'   => $this->getParameter('locale'),
            'theme'    => $this->getParameter('theme'),
            'timezone' => 0,
        ];

        $form = $this->createForm(new UserForm($this->getTranslator()), $default, [
            'action' => $this->generateUrl('admin_new_user'),
        ]);

        return $this->render('admin/users/dlg_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified user.
     *
     * @Action\Route("/dlg/edit/{id}", name="admin_dlg_edit_user", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int     $id User ID.
     *
     * @return  Response
     */
    public function dlgEditAction($id)
    {
        try {
            $command = new Users\FindUserCommand(['id' => $id]);

            $user = $this->getCommandBus()->handle($command);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(new UserForm($this->getTranslator()), $user, [
                'action' => $this->generateUrl('admin_edit_user', ['id' => $id]),
            ]);

            return $this->render('admin/users/dlg_user.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Verifies submitted form of "Export to CSV" parameters.
     *
     * @Action\Route("/export", name="admin_users_export")
     * @Action\Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function exportAction(Request $request)
    {
        $command = new ExportToCsvCommand($this->getFormData($request, 'export'));

        $violations = $this->getValidator()->validate($command);

        if (count($violations)) {
            return new JsonResponse($violations->get(0)->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }

    /**
     * Processes submitted form when new user is being created.
     *
     * @Action\Route("/new", name="admin_new_user")
     * @Action\Method("POST")
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
     * @Action\Route("/edit/{id}", name="admin_edit_user", requirements={"id"="\d+"})
     * @Action\Method("POST")
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

            $user = $this->getCommandBus()->handle($command);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $em->beginTransaction();

            $data       = $this->getFormData($request, 'user');
            $data['id'] = $id;

            if ($user->isLdap()) {
                $data['username'] = $user->getUsername();
                $data['fullname'] = $user->getFullname();
                $data['email']    = $user->getEmail();
            }

            $command = new Users\UpdateUserCommand($data);
            $this->getCommandBus()->handle($command);

            if ($this->getUser()->getId() == $id) {
                $this->get('session')->set('_locale', $command->locale);
            }

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
     * @Action\Route("/delete/{id}", name="admin_delete_user", requirements={"id"="\d+"})
     * @Action\Method("POST")
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
     * @Action\Route("/disable", name="admin_disable_user")
     * @Action\Method("POST")
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
     * @Action\Route("/enable", name="admin_enable_user")
     * @Action\Method("POST")
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
     * @Action\Route("/unlock/{id}", name="admin_unlock_user", requirements={"id"="\d+"})
     * @Action\Method("POST")
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
