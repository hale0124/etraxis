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

use eTraxis\Dictionary\FieldPermission;
use eTraxis\Dictionary\FieldType;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Field;
use eTraxis\Entity\Group;
use eTraxis\Entity\State;
use eTraxis\Form\FieldForm;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fields "GET" controller.
 *
 * @Action\Route("/fields", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class FieldsGetController extends Controller
{
    use ContainerTrait;

    /**
     * Returns JSON list of fields.
     *
     * @Action\Route("/list/{id}", name="admin_fields_list", requirements={"id"="\d+"})
     *
     * @param   State $state
     *
     * @return  JsonResponse
     */
    public function listAction(State $state): JsonResponse
    {
        return new JsonResponse($state->getFields());
    }

    /**
     * Shows specified field.
     *
     * @Action\Route("/{id}", name="admin_view_field", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   Field   $field
     *
     * @return  Response
     */
    public function viewAction(Request $request, Field $field): Response
    {
        return $this->render('admin/fields/view.html.twig', [
            'field' => $field,
            'tab'   => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with field's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_field_details", requirements={"id"="\d+"})
     *
     * @param   Field $field
     *
     * @return  Response
     */
    public function tabDetailsAction(Field $field): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        return $this->render(sprintf('admin/fields/tab_details_%s.html.twig', $field->getType()), [
            'field' => $field,
            'types' => FieldType::all(),
            'can'   => [
                'delete' => $authChecker->isGranted(Field::DELETE, $field),
            ],
        ]);
    }

    /**
     * Tab with field's permissions.
     *
     * @Action\Route("/tab/permissions/{id}", name="admin_tab_field_permissions", requirements={"id"="\d+"})
     *
     * @param   Field $field
     *
     * @return  Response
     */
    public function tabPermissionsAction(Field $field): Response
    {
        /** @var \eTraxis\Repository\GroupsRepository $repository */
        $repository = $this->getDoctrine()->getRepository(Group::class);

        return $this->render('admin/fields/tab_permissions.html.twig', [
            'field'       => $field,
            'groups'      => $repository->getGlobalGroups(),
            'roles'       => SystemRole::all(),
            'permissions' => FieldPermission::all(),
        ]);
    }

    /**
     * Renders dialog to create new field.
     *
     * @Action\Route("/new/{id}", name="admin_dlg_new_field", requirements={"id"="\d+"})
     *
     * @param   int $id State ID.
     *
     * @return  Response
     */
    public function newAction(int $id): Response
    {
        $form = $this->createForm(FieldForm::class, null, [
            'action' => $this->generateUrl('admin_new_field', ['id' => $id]),
        ]);

        return $this->render('admin/fields/dlg_field.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Renders dialog to edit specified field.
     *
     * @Action\Route("/edit/{id}", name="admin_dlg_edit_field", requirements={"id"="\d+"})
     *
     * @param   Field $field
     *
     * @return  Response
     */
    public function editAction(Field $field): Response
    {
        $form = $this->createForm(FieldForm::class, $field, [
            'action' => $this->generateUrl('admin_edit_field', ['id' => $field->getId()]),
        ]);

        return $this->render('admin/fields/dlg_field.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Loads permissions of the specified role for the specified field.
     *
     * @Action\Route("/permissions/{id}/{role}", name="admin_fields_load_role_permissions", requirements={"id"="\d+", "role"="\-\d+"})
     *
     * @param   Field $field
     * @param   int   $role
     *
     * @return  JsonResponse
     */
    public function loadRolePermissionsAction(Field $field, int $role): JsonResponse
    {
        return new JsonResponse($field->getRolePermission($role));
    }

    /**
     * Loads permissions of the specified group for the specified field.
     *
     * @Action\Route("/permissions/{id}/{group}", name="admin_fields_load_group_permissions", requirements={"id"="\d+", "group"="\d+"})
     *
     * @param   Field $field
     * @param   Group $group
     *
     * @return  JsonResponse
     */
    public function loadGroupPermissionsAction(Field $field, Group $group): JsonResponse
    {
        return new JsonResponse($field->getGroupPermission($group));
    }
}
