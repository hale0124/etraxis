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
use eTraxis\SimpleBus\Users;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Users "POST" controller.
 *
 * @Action\Route("/users", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class UsersPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new user is being created.
     *
     * @Action\Route("/new", name="admin_new_user")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request)
    {
        $data = $request->request->get('user');

        if ($data['password'] !== $data['confirmation']) {
            throw new BadRequestHttpException($this->container->get('translator')->trans('passwords.dont_match'));
        }

        $command = new Users\CreateUserCommand($data);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Processes submitted form when specified user is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_user", requirements={"id"="\d+"})
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

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $em->beginTransaction();

        $data = $request->request->get('user');

        if ($user->isLdap()) {
            $data['username'] = $user->getUsername();
            $data['fullname'] = $user->getFullname();
            $data['email']    = $user->getEmail();
        }

        $command = new Users\UpdateUserCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        if ($this->getUser() === $user) {
            $this->get('session')->set('_locale', $command->locale);
        }

        if (!$user->isLdap() && $data['password']) {

            if ($data['password'] !== $data['confirmation']) {
                throw new BadRequestHttpException($this->container->get('translator')->trans('passwords.dont_match'));
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

    /**
     * Deletes specified user.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_user", requirements={"id"="\d+"})
     *
     * @param   int $id User ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        $command = new Users\DeleteUserCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Disables specified users.
     *
     * @Action\Route("/disable", name="admin_disable_user")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function disableAction(Request $request)
    {
        $data = $request->request->all();

        $command = new Users\DisableUsersCommand($data);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Enables specified users.
     *
     * @Action\Route("/enable", name="admin_enable_user")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function enableAction(Request $request)
    {
        $data = $request->request->all();

        $command = new Users\EnableUsersCommand($data);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Unlocks specified user.
     *
     * @Action\Route("/unlock/{id}", name="admin_unlock_user", requirements={"id"="\d+"})
     *
     * @param   int $id User ID.
     *
     * @return  JsonResponse
     */
    public function unlockAction($id)
    {
        $command = new Users\UnlockUserCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Adds user to specified groups.
     *
     * @Action\Route("/groups/add/{id}", name="admin_users_add_groups", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  JsonResponse
     */
    public function addGroupsAction(Request $request, $id)
    {
        $data = $request->request->all();

        $command = new Users\AddGroupsCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Removes user from specified groups.
     *
     * @Action\Route("/groups/remove/{id}", name="admin_users_remove_groups", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id User ID.
     *
     * @return  JsonResponse
     */
    public function removeGroupsAction(Request $request, $id)
    {
        $data = $request->request->all();

        $command = new Users\RemoveGroupsCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
