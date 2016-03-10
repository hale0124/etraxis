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
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
     * @param   int $id State ID.
     *
     * @return  Response|JsonResponse
     */
    public function listAction($id)
    {
        try {
            /** @var \eTraxis\Repository\FieldsRepository $repository */
            $repository = $this->getDoctrine()->getRepository(Field::class);

            return new JsonResponse($repository->getFields($id));
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Shows specified field.
     *
     * @Action\Route("/{id}", name="admin_view_field", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Field ID.
     *
     * @return  Response
     */
    public function viewAction(Request $request, $id)
    {
        try {
            $field = $this->getDoctrine()->getRepository(Field::class)->find($id);

            if (!$field) {
                throw $this->createNotFoundException();
            }

            return $this->render('admin/fields/view.html.twig', [
                'field' => $field,
                'tab'   => $request->get('tab', 0),
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }

    /**
     * Tab with field's details.
     *
     * @Action\Route("/tab/details/{id}", name="admin_tab_field_details", requirements={"id"="\d+"})
     *
     * @param   int $id Field ID.
     *
     * @return  Response
     */
    public function tabDetailsAction($id)
    {
        try {
            /** @var Field $field */
            $field = $this->getDoctrine()->getRepository(Field::class)->find($id);

            if (!$field) {
                throw $this->createNotFoundException();
            }

            /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
            $authChecker = $this->get('security.authorization_checker');

            return $this->render(sprintf('admin/fields/tab_details_%s.html.twig', $field->getTypeEx()), [
                'field' => $field,
                'types' => FieldType::getCollection(),
                'can'   => [
                    'delete' => $authChecker->isGranted(Field::DELETE, $field),
                ],
            ]);
        }
        catch (HttpException $e) {
            return new Response($e->getMessage(), $e->getStatusCode());
        }
    }
}
