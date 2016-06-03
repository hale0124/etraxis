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

use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use eTraxis\Form\StateForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * States "GET" controller.
 *
 * @Action\Route("/states", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class StatesGetController extends Controller
{
    /**
     * Returns JSON list of states.
     *
     * @Action\Route("/list/{id}", name="admin_states_list", requirements={"id"="\d+"})
     *
     * @param   Template $template
     *
     * @return  JsonResponse
     */
    public function listAction(Template $template): JsonResponse
    {
        return new JsonResponse($template->getStates());
    }

    /**
     * Shows specified state.
     *
     * @Action\Route("/{id}", name="admin_view_state", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   State   $state
     *
     * @return  Response
     */
    public function viewAction(Request $request, State $state): Response
    {
        return $this->render('admin/states/view.html.twig', [
            'state' => $state,
            'tab'   => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with state's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_state_details", requirements={"id"="\d+"})
     *
     * @param   State $state
     *
     * @return  Response
     */
    public function tabDetailsAction(State $state): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        return $this->render('admin/states/tab_details.html.twig', [
            'state'        => $state,
            'types'        => StateType::all(),
            'responsibles' => StateResponsible::all(),
            'can'          => [
                'delete'  => $authChecker->isGranted(State::DELETE, $state),
                'initial' => $authChecker->isGranted(State::INITIAL, $state),
            ],
        ]);
    }

    /**
     * Tab with state's transitions.
     *
     * @Action\Route("/tab/transitions/{id}", name="admin_tab_state_transitions", requirements={"id"="\d+"})
     *
     * @param   State $state
     *
     * @return  Response
     */
    public function tabTransitionsAction(State $state): Response
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return $this->render('admin/states/tab_transitions.html.twig', [
            'state'  => $state,
            'groups' => $repository->getGlobalGroups(),
            'roles'  => SystemRole::all(),
        ]);
    }

    /**
     * Tab with state's responsible groups.
     *
     * @Action\Route("/tab/responsibles/{id}", name="admin_tab_state_responsibles", requirements={"id"="\d+"})
     *
     * @param   State $state
     *
     * @return  Response
     */
    public function tabResponsiblesAction(State $state): Response
    {
        return $this->render('admin/states/tab_responsibles.html.twig', [
            'state' => $state,
        ]);
    }

    /**
     * Renders dialog to create new state.
     *
     * @Action\Route("/new/{id}", name="admin_dlg_new_state", requirements={"id"="\d+"})
     *
     * @param   int $id Template ID.
     *
     * @return  Response
     */
    public function newAction(int $id): Response
    {
        $form = $this->createForm(StateForm::class, null, [
            'action' => $this->generateUrl('admin_new_state', ['id' => $id]),
        ]);

        return $this->render('admin/states/dlg_state.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified state.
     *
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_state", requirements={"id"="\d+"})
     *
     * @param   State $state
     *
     * @return  Response
     */
    public function editAction(State $state): Response
    {
        $form = $this->createForm(StateForm::class, $state, [
            'action' => $this->generateUrl('admin_edit_state', ['id' => $state->getId()]),
        ]);

        return $this->render('admin/states/dlg_state.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Loads transitions of the specified role for the specified state.
     *
     * @Action\Route("/transitions/{id}/{role}", name="admin_states_load_role_transitions", requirements={"id"="\d+", "role"="\D+"})
     *
     * @param   State  $state
     * @param   string $role
     *
     * @return  JsonResponse
     */
    public function loadRoleTransitionsAction(State $state, string $role): JsonResponse
    {
        return new JsonResponse($state->getRoleTransitions($role));
    }

    /**
     * Loads transitions of the specified group for the specified state.
     *
     * @Action\Route("/transitions/{id}/{group}", name="admin_states_load_group_transitions", requirements={"id"="\d+", "group"="\d+"})
     *
     * @param   State $state
     * @param   Group $group
     *
     * @return  JsonResponse
     */
    public function loadGroupTransitionsAction(State $state, Group $group): JsonResponse
    {
        return new JsonResponse($state->getGroupTransitions($group));
    }
}
