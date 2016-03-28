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

use eTraxis\Entity\Field;
use eTraxis\SimpleBus\Fields;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use SimpleBus\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Fields "POST" controller.
 *
 * @Action\Route("/fields", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class FieldsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new field is being created.
     *
     * @Action\Route("/new/{id}", name="admin_new_field", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id State ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, $id)
    {
        try {
            $data = $request->request->get('field');

            switch ($data['type']) {

                case Field::TYPE_NUMBER:
                    $command = new Fields\CreateNumberFieldCommand($data + $data['asNumber'], ['state' => $id]);
                    break;

                case Field::TYPE_DECIMAL:
                    $command = new Fields\CreateDecimalFieldCommand($data + $data['asDecimal'], ['state' => $id]);
                    break;

                case Field::TYPE_STRING:
                    $command = new Fields\CreateStringFieldCommand($data + $data['asString'], ['state' => $id]);
                    break;

                case Field::TYPE_TEXT:
                    $command = new Fields\CreateTextFieldCommand($data + $data['asText'], ['state' => $id]);
                    break;

                case Field::TYPE_CHECKBOX:
                    $command = new Fields\CreateCheckboxFieldCommand($data + $data['asCheckbox'], ['state' => $id]);
                    break;

                case Field::TYPE_LIST:
                    $command = new Fields\CreateListFieldCommand($data, ['state' => $id]);
                    break;

                case Field::TYPE_RECORD:
                    $command = new Fields\CreateRecordFieldCommand($data, ['state' => $id]);
                    break;

                case Field::TYPE_DATE:
                    $command = new Fields\CreateDateFieldCommand($data + $data['asDate'], ['state' => $id]);
                    break;

                case Field::TYPE_DURATION:
                    $command = new Fields\CreateDurationFieldCommand($data + $data['asDuration'], ['state' => $id]);
                    break;

                default:
                    throw new BadRequestHttpException();
            }

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
     * Processes submitted form when specified field is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_field", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Field ID.
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, $id)
    {
        try {
            /** @var Field $field */
            $field = $this->getDoctrine()->getRepository(Field::class)->find($id);

            if (!$field) {
                throw $this->createNotFoundException();
            }

            $data = $request->request->get('field');

            switch ($field->getType()) {

                case Field::TYPE_NUMBER:
                    $command = new Fields\UpdateNumberFieldCommand($data + $data['asNumber'], ['id' => $id]);
                    break;

                case Field::TYPE_DECIMAL:
                    $command = new Fields\UpdateDecimalFieldCommand($data + $data['asDecimal'], ['id' => $id]);
                    break;

                case Field::TYPE_STRING:
                    $command = new Fields\UpdateStringFieldCommand($data + $data['asString'], ['id' => $id]);
                    break;

                case Field::TYPE_TEXT:
                    $command = new Fields\UpdateTextFieldCommand($data + $data['asText'], ['id' => $id]);
                    break;

                case Field::TYPE_CHECKBOX:
                    $command = new Fields\UpdateCheckboxFieldCommand($data + $data['asCheckbox'], ['id' => $id, 'required' => false]);
                    break;

                case Field::TYPE_LIST:
                    $command = new Fields\UpdateListFieldCommand($data, ['id' => $id]);
                    break;

                case Field::TYPE_RECORD:
                    $command = new Fields\UpdateRecordFieldCommand($data, ['id' => $id]);
                    break;

                case Field::TYPE_DATE:
                    $command = new Fields\UpdateDateFieldCommand($data + $data['asDate'], ['id' => $id]);
                    break;

                case Field::TYPE_DURATION:
                    $command = new Fields\UpdateDurationFieldCommand($data + $data['asDuration'], ['id' => $id]);
                    break;

                default:
                    throw new BadRequestHttpException();
            }

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
     * Deletes specified field.
     *
     * @Action\Route("/delete/{id}", name="admin_delete_field", requirements={"id"="\d+"})
     *
     * @param   int $id Field ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction($id)
    {
        try {
            $command = new Fields\DeleteFieldCommand(['id' => $id]);
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
