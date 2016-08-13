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

use eTraxis\CommandBus\States;
use eTraxis\Dictionary\StateResponsible;
use eTraxis\Entity\Group;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * States "POST" controller.
 *
 * @Action\Route("/states", condition="request.isXmlHttpRequest()")
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
    public function newAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->get('state');

        if (!array_key_exists('responsible', $data)) {
            $data['responsible'] = StateResponsible::KEEP;
        }

        $command = new States\CreateStateCommand($data, ['template' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
    public function editAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->get('state');

        $command = new States\UpdateStateCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
    public function deleteAction(int $id): JsonResponse
    {
        $command = new States\DeleteStateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
    public function initialAction(int $id): JsonResponse
    {
        $command = new States\SetInitialStateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Saves transitions of the specified role for the specified state.
     *
     * @Action\Route("/transitions/{id}/{role}", name="admin_states_save_role_transitions", requirements={"id"="\d+", "role"="\D+"})
     *
     * @param   Request $request
     * @param   int     $id
     * @param   string  $role
     *
     * @return  JsonResponse
     */
    public function saveRoleTransitionsAction(Request $request, int $id, string $role): JsonResponse
    {
        $command = new States\SetRoleStateTransitionsCommand([
            'id'          => $id,
            'role'        => $role,
            'transitions' => $request->request->get('transitions', []),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Saves transitions of the specified group for the specified state.
     *
     * @Action\Route("/transitions/{id}/{group}", name="admin_states_save_group_transitions", requirements={"id"="\d+", "group"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id
     * @param   Group   $group
     *
     * @return  JsonResponse
     */
    public function saveGroupTransitionsAction(Request $request, int $id, Group $group): JsonResponse
    {
        $command = new States\SetGroupStateTransitionsCommand([
            'id'          => $id,
            'group'       => $group->getId(),
            'transitions' => $request->request->get('transitions', []),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Adds responsible groups to specified state.
     *
     * @Action\Route("/responsibles/add/{id}", name="admin_states_add_responsibles", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id
     *
     * @return  JsonResponse
     */
    public function addResponsiblesAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        $command = new States\AddStateResponsibleGroupsCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Removes responsible groups to specified state.
     *
     * @Action\Route("/responsibles/remove/{id}", name="admin_states_remove_responsibles", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id
     *
     * @return  JsonResponse
     */
    public function removeResponsiblesAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->all();

        $command = new States\RemoveStateResponsibleGroupsCommand($data, ['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
