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

use eTraxis\Collection\StateResponsible;
use eTraxis\Collection\StateType;
use eTraxis\Collection\SystemRole;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Form\StateForm;
use eTraxis\Traits\ContainerTrait;
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
    use ContainerTrait;

    /**
     * Returns JSON list of states.
     *
     * @Action\Route("/list/{id}", name="admin_states_list", requirements={"id"="\d+"})
     *
     * @param   int $id Template ID.
     *
     * @return  JsonResponse
     */
    public function listAction($id)
    {
        /** @var \eTraxis\Repository\StatesRepository $repository */
        $repository = $this->getDoctrine()->getRepository(State::class);

        return new JsonResponse($repository->getStates($id));
    }

    /**
     * Shows specified state.
     *
     * @Action\Route("/{id}", name="admin_view_state", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id State ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id)
    {
        $state = $this->getDoctrine()->getRepository(State::class)->find($id);

        if (!$state) {
            throw $this->createNotFoundException();
        }

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
     * @param   int $id State ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        $state = $this->getDoctrine()->getRepository(State::class)->find($id);

        if (!$state) {
            throw $this->createNotFoundException();
        }

        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        return $this->render('admin/states/tab_details.html.twig', [
            'state'        => $state,
            'types'        => StateType::getCollection(),
            'responsibles' => StateResponsible::getCollection(),
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
     * @param   int $id State ID.
     *
     * @return  Response
     */
    public function tabTransitionsAction($id)
    {
        /** @var State $state */
        $state = $this->getDoctrine()->getRepository(State::class)->find($id);

        if (!$this) {
            throw $this->createNotFoundException();
        }

        /** @var \eTraxis\Repository\StatesRepository $repository */
        $repository = $this->getDoctrine()->getRepository(State::class);

        $transitions = $repository->getStates($state->getTemplateId());

        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return $this->render('admin/states/tab_transitions.html.twig', [
            'state'       => $state,
            'locals'      => $repository->getLocalGroups($state->getTemplate()->getProjectId()),
            'globals'     => $repository->getGlobalGroups(),
            'transitions' => $transitions,
            'role'        => [
                'author'      => SystemRole::AUTHOR,
                'responsible' => SystemRole::RESPONSIBLE,
                'registered'  => SystemRole::REGISTERED,
            ],
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
    public function newAction($id)
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
     * @param   int $id State ID.
     *
     * @return  Response
     */
    public function editAction($id)
    {
        $state = $this->getDoctrine()->getRepository(State::class)->find($id);

        if (!$state) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(StateForm::class, $state, [
            'action' => $this->generateUrl('admin_edit_state', ['id' => $id]),
        ]);

        return $this->render('admin/states/dlg_state.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Loads transitions of the specified state.
     *
     * @Action\Route("/transitions/{id}/{group}", name="admin_load_state_transitions", requirements={"id"="\d+", "group"="[\-]?\d+"})
     *
     * @param   int $id    State ID.
     * @param   int $group Group ID or system role.
     *
     * @return  JsonResponse
     */
    public function loadTransitionsAction($id, $group)
    {
        /** @var \eTraxis\Repository\StatesRepository $repository */
        $repository = $this->getDoctrine()->getRepository(State::class);

        /** @var State $state */
        $state = $repository->find($id);

        if (!$state) {
            throw $this->createNotFoundException();
        }

        $transitions = array_key_exists($group, SystemRole::getCollection())
            ? $repository->getRoleTransitions($id, $group)
            : $repository->getGroupTransitions($id, $group);

        return new JsonResponse($transitions);
    }
}
