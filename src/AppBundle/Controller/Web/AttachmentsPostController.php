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

use eTraxis\CommandBus\Attachments;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Attachments "POST" controller.
 *
 * @Action\Route("/files", condition="request.isXmlHttpRequest()")
 * @Action\Method("POST")
 */
class AttachmentsPostController extends Controller
{
    use ContainerTrait;

    /**
     * Processes submitted form when new file is being attached.
     *
     * @Action\Route("/attach/{id}", name="web_attach_file", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   int     $id Record ID.
     *
     * @return  JsonResponse
     */
    public function attachAction(Request $request, int $id): JsonResponse
    {
        /** @var UploadedFile[] $files */
        $files = $request->files->get('attachment');

        $command = new Attachments\AttachFileCommand([
            'record' => $id,
            'file'   => $files['file'],
        ]);

        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }

    /**
     * Deletes specified attachment.
     *
     * @Action\Route("/delete/{id}", name="web_delete_file", requirements={"id"="\d+"})
     *
     * @param   int $id Attachment ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        $command = new Attachments\DeleteFileCommand(['id' => $id]);
        $this->getCommandBus()->handle($command);

        return new JsonResponse();
    }
}
