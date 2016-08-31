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
use Symfony\Component\HttpFoundation\Response;

/**
 * Comments "POST" controller.
 *
 * @Action\Route("/comments", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class CommentsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new comment is being previewed.
     *
     * @Action\Route("/preview", name="web_preview_comment")
     *
     * @param   Request $request
     *
     * @return  Response
     */
    public function previewAction(Request $request): Response
    {
        $data = $request->request->get('comment');

        return $this->render('web/records/_preview.html.twig', [
            'text'      => $data['text'],
            'isPrivate' => $data['private'],
        ]);
    }

    /**
     * Processes submitted form when new comment is being posted.
     *
     * @Action\Route("/new/{id}", name="web_new_comment", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Record ID.
     *
     * @return  JsonResponse
     */
    public function newAction(Request $request, int $id): JsonResponse
    {
        $data = $request->request->get('comment');

        $command = new Records\AddCommentCommand($data, [
            'record' => $id,
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
