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

use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\SimpleBus\States;
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
    public function newAction(Request $request, $id)
    {
        $data = $request->request->get('state');

        if (!array_key_exists('responsible', $data)) {
            $data['responsible'] = State::RESPONSIBLE_KEEP;
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
    public function editAction(Request $request, $id)
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
    public function deleteAction($id)
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
    public function initialAction($id)
    {
        $command = new States\SetInitialStateCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Saves transitions of the specified role for the specified state.
     *
     * @Action\Route("/transitions/{id}/{role}", name="admin_states_save_role_transitions", requirements={"id"="\d+", "role"="\-\d+"})
     *
     * @param   Request $request
     * @param   int     $id
     * @param   int     $role
     *
     * @return  JsonResponse
     */
    public function saveRoleTransitionsAction(Request $request, $id, $role)
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
    public function saveGroupTransitionsAction(Request $request, $id, Group $group)
    {
        $command = new States\SetGroupStateTransitionsCommand([
            'id'          => $id,
            'group'       => $group->getId(),
            'transitions' => $request->request->get('transitions', []),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
