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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function newAction(Request $request, int $id): JsonResponse
    {
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

    /**
     * Processes submitted form when specified field is being edited.
     *
     * @Action\Route("/edit/{id}", name="admin_edit_field", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   Field   $field
     *
     * @return  JsonResponse
     */
    public function editAction(Request $request, Field $field): JsonResponse
    {
        $data = $request->request->get('field');

        switch ($field->getType()) {

            case Field::TYPE_NUMBER:
                $command = new Fields\UpdateNumberFieldCommand($data + $data['asNumber'], ['id' => $field->getId()]);
                break;

            case Field::TYPE_DECIMAL:
                $command = new Fields\UpdateDecimalFieldCommand($data + $data['asDecimal'], ['id' => $field->getId()]);
                break;

            case Field::TYPE_STRING:
                $command = new Fields\UpdateStringFieldCommand($data + $data['asString'], ['id' => $field->getId()]);
                break;

            case Field::TYPE_TEXT:
                $command = new Fields\UpdateTextFieldCommand($data + $data['asText'], ['id' => $field->getId()]);
                break;

            case Field::TYPE_CHECKBOX:
                $command = new Fields\UpdateCheckboxFieldCommand($data + $data['asCheckbox'], ['id' => $field->getId(), 'required' => false]);
                break;

            case Field::TYPE_LIST:
                $command = new Fields\UpdateListFieldCommand($data, ['id' => $field->getId()]);
                break;

            case Field::TYPE_RECORD:
                $command = new Fields\UpdateRecordFieldCommand($data, ['id' => $field->getId()]);
                break;

            case Field::TYPE_DATE:
                $command = new Fields\UpdateDateFieldCommand($data + $data['asDate'], ['id' => $field->getId()]);
                break;

            case Field::TYPE_DURATION:
                $command = new Fields\UpdateDurationFieldCommand($data + $data['asDuration'], ['id' => $field->getId()]);
                break;

            default:
                throw new BadRequestHttpException();
        }

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
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
    public function deleteAction(int $id): JsonResponse
    {
        $command = new Fields\DeleteFieldCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
