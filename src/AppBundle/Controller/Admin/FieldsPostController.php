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
use eTraxis\SimpleBus\Middleware\ValidationException;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
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
            $data = $this->getFormData($request, 'field', ['state' => $id]);

            switch ($data['type']) {

                case Field::TYPE_NUMBER:
                    $command = new Fields\CreateNumberFieldCommand($data + $data['number']);
                    break;

                case Field::TYPE_DECIMAL:
                    $command = new Fields\CreateDecimalFieldCommand($data + $data['decimal']);
                    break;

                case Field::TYPE_STRING:
                    $command = new Fields\CreateStringFieldCommand($data + $data['string']);
                    break;

                case Field::TYPE_TEXT:
                    $command = new Fields\CreateTextFieldCommand($data + $data['text']);
                    break;

                case Field::TYPE_CHECKBOX:
                    $command = new Fields\CreateCheckboxFieldCommand($data + $data['checkbox']);
                    break;

                case Field::TYPE_LIST:
                    $command = new Fields\CreateListFieldCommand($data);
                    break;

                case Field::TYPE_RECORD:
                    $command = new Fields\CreateRecordFieldCommand($data);
                    break;

                case Field::TYPE_DATE:
                    $command = new Fields\CreateDateFieldCommand($data + $data['date']);
                    break;

                case Field::TYPE_DURATION:
                    $command = new Fields\CreateDurationFieldCommand($data + $data['duration']);
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
}
