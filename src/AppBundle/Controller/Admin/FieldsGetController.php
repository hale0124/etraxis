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

use eTraxis\Collection\FieldType;
use eTraxis\Entity\Field;
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
    public function listAction(State $state)
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
    public function viewAction(Request $request, Field $field)
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
    public function tabDetailsAction(Field $field)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        return $this->render(sprintf('admin/fields/tab_details_%s.html.twig', $field->getType()), [
            'field' => $field,
            'types' => FieldType::getCollection(),
            'can'   => [
                'delete' => $authChecker->isGranted(Field::DELETE, $field),
            ],
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
    public function newAction($id)
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
    public function editAction(Field $field)
    {
        $form = $this->createForm(FieldForm::class, $field, [
            'action' => $this->generateUrl('admin_edit_field', ['id' => $field->getId()]),
        ]);

        return $this->render('admin/fields/dlg_field.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
