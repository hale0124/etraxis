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

use eTraxis\CommandBus\Middleware\ValidationException;
use eTraxis\Dictionary\AuthenticationProvider;
use eTraxis\Entity\User;
use eTraxis\Form\UserForm;
use eTraxis\Service\Export\ExportCsvQuery;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Voter\UserVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Users "GET" controller.
 *
 * @Action\Route("/users", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class UsersGetController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of users.
     *
     * @Action\Route("/", name="admin_users", condition="")
     *
     * @return  Response
     */
    public function indexAction(): Response
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
     * @return  JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        /** @var \DataTables\DataTablesInterface $datatables */
        $datatables = $this->container->get('datatables');
        $results    = $datatables->handle($request, 'eTraxis:User');

        return new JsonResponse($results);
    }

    /**
     * Exports list of users as CSV file.
     *
     * @Action\Route("/csv", name="admin_users_csv", condition="")
     *
     * @param   Request $request
     *
     * @return  StreamedResponse
     */
    public function csvAction(Request $request): StreamedResponse
    {
        $request->query->set('start', 0);
        $request->query->set('length', -1);

        /** @var \DataTables\DataTablesInterface $datatables */
        $datatables = $this->container->get('datatables');
        $results    = $datatables->handle($request, 'eTraxis:User');

        $users = array_map(function ($user) {
            return array_slice($user, 1, 6);
        }, $results['data']);

        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        array_unshift($users, [
            $translator->trans('user.username'),
            $translator->trans('user.fullname'),
            $translator->trans('user.email'),
            $translator->trans('permissions'),
            $translator->trans('security.authentication'),
            $translator->trans('description'),
        ]);

        $query = new ExportCsvQuery($request->query->get('export'));

        /** @var \Symfony\Component\Validator\ConstraintViolationInterface[] $violations */
        $violations = $this->get('validator')->validate($query);

        if (count($violations)) {
            throw new ValidationException($violations);
        }

        /** @var \eTraxis\Service\ExportInterface $export */
        $export = $this->get('etraxis.export');

        return $export->exportCsv($query, $users);
    }

    /**
     * Shows specified user.
     *
     * @Action\Route("/{id}", name="admin_view_user", condition="", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   User    $user
     *
     * @return  Response
     */
    public function viewAction(Request $request, User $user): Response
    {
        return $this->render('admin/users/view.html.twig', [
            'user' => $user,
            'tab'  => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with user's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_user_details", requirements={"id"="\d+"})
     *
     * @param   User $user
     *
     * @return  Response
     */
    public function tabDetailsAction(User $user): Response
    {
        return $this->render('admin/users/tab_details.html.twig', [
            'user'      => $user,
            'providers' => AuthenticationProvider::all(),
            'can'       => [
                'delete'  => $this->isGranted(UserVoter::DELETE, $user),
                'disable' => $this->isGranted(UserVoter::DISABLE, $user),
                'enable'  => $this->isGranted(UserVoter::ENABLE, $user),
                'unlock'  => $this->isGranted(UserVoter::UNLOCK, $user),
            ],
        ]);
    }

    /**
     * Tab with user's groups.
     *
     * @Action\Route("/tab/groups/{id}", name="admin_tab_user_groups", requirements={"id"="\d+"})
     *
     * @param   User $user
     *
     * @return  Response
     */
    public function tabGroupsAction(User $user): Response
    {
        return $this->render('admin/users/tab_groups.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Renders dialog to create new user.
     *
     * @Action\Route("/new", name="admin_dlg_new_user")
     *
     * @return  Response
     */
    public function newAction(): Response
    {
        $settings = [
            'locale'   => $this->getParameter('locale'),
            'theme'    => $this->getParameter('theme'),
            'timezone' => $this->getUser()->getTimezone(),
        ];

        $form = $this->createForm(UserForm::class, $settings, [
            'action' => $this->generateUrl('admin_new_user'),
        ]);

        return $this->render('admin/users/dlg_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified user.
     *
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_user", requirements={"id"="\d+"})
     *
     * @param   User $user
     *
     * @return  Response
     */
    public function editAction(User $user): Response
    {
        $form = $this->createForm(UserForm::class, $user, [
            'action' => $this->generateUrl('admin_edit_user', ['id' => $user->getId()]),
        ]);

        return $this->render('admin/users/dlg_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
