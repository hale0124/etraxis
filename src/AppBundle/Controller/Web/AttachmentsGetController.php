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

use eTraxis\Entity\Attachment;
use eTraxis\Voter\RecordVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Attachments "GET" controller.
 *
 * @Action\Route("/files")
 * @Action\Method("GET")
 */
class AttachmentsGetController extends Controller
{
    /**
     * Downloads specified attachment.
     *
     * @Action\Route("/{id}", name="web_download_file", requirements={"id"="\d+"})
     *
     * @param   Attachment $attachment
     *
     * @return  BinaryFileResponse
     */
    public function downloadAction(Attachment $attachment): BinaryFileResponse
    {
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $attachment->getEvent()->getRecord());

        if ($attachment->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $filename = $attachment->getAbsolutePath($this->getParameter('files_path'));
        $response = new BinaryFileResponse($filename);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $attachment->getName());
        $response->setPrivate();

        return $response;
    }
}
