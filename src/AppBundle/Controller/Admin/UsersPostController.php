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
use eTraxis\SimpleBus\Users;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        try {
            $data = $this->getFormData($request, 'user');

            if ($data['password'] != $data['confirmation']) {
                throw new BadRequestHttpException($this->getTranslator()->trans('passwords.dont_match'));
            }

            $command = new Users\CreateUserCommand($data);
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

        try {
            /** @var \eTraxis\Entity\User $user */
            $user = $this->getDoctrine()->getRepository('eTraxis:User')->find($id);

            if (!$user) {
                throw $this->createNotFoundException();
            }

            $em->beginTransaction();

            $data = $this->getFormData($request, 'user', ['id' => $id]);

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
                    throw new BadRequestHttpException($this->getTranslator()->trans('passwords.dont_match'));
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
            return new JsonResponse($e->getMessages(), $e->getStatusCode());
        }
        catch (HttpException $e) {
            return new JsonResponse($e->getMessage(), $e->getStatusCode());
        }
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
        try {
            $command = new Users\DeleteUserCommand(['id' => $id]);
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
        try {
            $data = $this->getFormData($request);

            $command = new Users\DisableUsersCommand($data);
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
        try {
            $data = $this->getFormData($request);

            $command = new Users\EnableUsersCommand($data);
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
        try {
            $command = new Users\UnlockUserCommand(['id' => $id]);
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
        try {
            $data = $this->getFormData($request, null, ['id' => $id]);

            $command = new Users\AddGroupsCommand($data);
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
        try {
            $data = $this->getFormData($request, null, ['id' => $id]);

            $command = new Users\RemoveGroupsCommand($data);
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
