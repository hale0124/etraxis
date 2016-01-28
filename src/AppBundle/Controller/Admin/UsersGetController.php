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

use eTraxis\Entity\User;
use eTraxis\Form\UserForm;
use eTraxis\Service\ExportCsvQuery;
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Users "GET" controller.
 *
 * @Action\Route("/users")
 * @Action\Method("GET")
 */
class UsersGetController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of users.
     *
     * @Action\Route("/", name="admin_users")
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
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Exports list of users as CSV file.
     *
     * @Action\Route("/csv", name="admin_users_csv")
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
        catch (ValidationException $e) {
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Shows specified user.
     *
     * @Action\Route("/{id}", name="admin_view_user", requirements={"id"="\d+"})
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
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with user's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_user_details", requirements={"id"="\d+"})
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
                    'delete'  => $authChecker->isGranted(User::DELETE, $user),
                    'disable' => $authChecker->isGranted(User::DISABLE, $user),
                    'enable'  => $authChecker->isGranted(User::ENABLE, $user),
                    'unlock'  => $authChecker->isGranted(User::UNLOCK, $user),
                ],
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with user's groups.
     *
     * @Action\Route("/tab/groups/{id}", name="admin_tab_user_groups", requirements={"id"="\d+"})
     *
     * @param   int $id User ID.
     *
     * @return  Response
     */
    public function tabGroupsAction($id)
    {
        /** @var \eTraxis\Repository\UsersRepository $repository */
        $repository = $this->getDoctrine()->getRepository('eTraxis:User');

        $user = $repository->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/users/tab_groups.html.twig', [
            'user'   => $user,
            'groups' => $repository->getUserGroups($id),
            'others' => $repository->getOtherGroups($id),
        ]);
    }

    /**
     * Renders dialog to create new user.
     *
     * @Action\Route("/new")
     *
     * @return  Response
     */
    public function newAction()
    {
        $default = [
            'locale'   => $this->getParameter('locale'),
            'theme'    => $this->getParameter('theme'),
            'timezone' => 0,
        ];

        $form = $this->createForm(UserForm::class, $default, [
            'action' => $this->generateUrl('admin_new_user'),
        ]);

        return $this->render('admin/users/dlg_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified user.
     *
     * @Action\Route("/edit/{id}", requirements={"id"="\d+"})
     *
     * @param   int     $id User ID.
     *
     * @return  Response
     */
    public function editAction($id)
    {
        try {
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $form = $this->createForm(UserForm::class, $user, [
                'action' => $this->generateUrl('admin_edit_user', ['id' => $id]),
            ]);

            return $this->render('admin/users/dlg_user.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }
}
