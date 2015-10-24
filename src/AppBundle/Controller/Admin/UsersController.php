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

use eTraxis\CommandBus\CommandException;
use eTraxis\CommandBus\Users;
use eTraxis\CommandBus\ValidationException;
use eTraxis\DataTables\DataTableException;
use eTraxis\Form\UserForm;
use eTraxis\Service\ExportCsvQuery;
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
            $datatables = $this->getDataTables();
            $results    = $datatables->handle($request, 'eTraxis:User');

            return new JsonResponse($results);
        }
        catch (DataTableException $e) {
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
            $request->query->set('start', 0);
            $request->query->set('length', -1);

            $datatables = $this->getDataTables();
            $results    = $datatables->handle($request, 'eTraxis:User');

            $users = array_map(function ($user) {
                return array_slice($user, 1, 6);
            }, $results['data']);

            array_unshift($users, [
                $this->getTranslator()->trans('user.username'),
                $this->getTranslator()->trans('user.fullname'),
                $this->getTranslator()->trans('user.email'),
                $this->getTranslator()->trans('permissions'),
                $this->getTranslator()->trans('security.authentication'),
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

            return $export->exportCsv($query, $users);
        }
        catch (DataTableException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
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
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

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
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
            $authChecker = $this->get('security.authorization_checker');

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
     * Tab with user's groups.
     *
     * @Action\Route("/tab/groups/{id}", name="admin_tab_user_groups", requirements={"id"="\d+"})
     * @Action\Method("GET")
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function tabGroupsAction($id)
    {
        try {
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $groups = $this->getCommandBus()->handle(
                new Users\GetUserGroupsCommand(['id' => $id])
            );

            $others = $this->getCommandBus()->handle(
                new Users\GetOtherGroupsCommand(['id' => $id])
            );

            return $this->render('admin/users/tab_groups.html.twig', [
                'user'   => $user,
                'groups' => $groups,
                'others' => $others,
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
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
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(new UserForm($this->getTranslator()), $user, [
                'action' => $this->generateUrl('admin_edit_user', ['id' => $id]),
            ]);

            return $this->render('admin/users/dlg_user.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
        catch (ValidationException $e) {
            return new Response($e->getMessage(), $e->getCode());
        }
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
                throw new CommandException($this->getTranslator()->trans('passwords.dont_match'));
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
            /** @var \eTraxis\Entity\User $user */
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

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

            if (!$user->isLdap() && $data['password']) {

                if ($data['password'] != $data['confirmation']) {
                    throw new CommandException($this->getTranslator()->trans('passwords.dont_match'));
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

    /**
     * Adds user to specified groups.
     *
     * @Action\Route("/groups/add/{id}", name="admin_users_add_groups", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  JsonResponse
     */
    public function addGroupsAction(Request $request, $id)
    {
        try {
            $command = new Users\AddGroupsCommand(
                array_merge(['id' => $id], $request->request->all())
            );

            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
    }

    /**
     * Removes user from specified groups.
     *
     * @Action\Route("/groups/remove/{id}", name="admin_users_remove_groups", requirements={"id"="\d+"})
     * @Action\Method("POST")
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  JsonResponse
     */
    public function removeGroupsAction(Request $request, $id)
    {
        try {
            $command = new Users\RemoveGroupsCommand(
                array_merge(['id' => $id], $request->request->all())
            );

            $this->getCommandBus()->handle($command);

            return new JsonResponse();
        }
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getCode());
        }
    }
}
