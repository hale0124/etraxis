<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Web;

use eTraxis\CommandBus\Records;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Records "POST" controller.
 *
 * @Action\Route("/records", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class RecordsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Marks specified records as read.
     *
     * @Action\Route("/read", name="web_read_records")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function readAction(Request $request): JsonResponse
    {
        $data = $request->request->all();

        $command = new Records\MarkRecordsAsReadCommand($data, [
            'user' => $this->getUser()->getId(),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Marks specified records as unread.
     *
     * @Action\Route("/unread", name="web_unread_records")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function unreadAction(Request $request): JsonResponse
    {
        $data = $request->request->all();

        $command = new Records\MarkRecordsAsUnreadCommand($data, [
            'user' => $this->getUser()->getId(),
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
