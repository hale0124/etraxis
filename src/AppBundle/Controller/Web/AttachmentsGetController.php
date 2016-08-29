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
use eTraxis\Entity\Record;
use eTraxis\Form\AttachFileForm;
use eTraxis\Voter\RecordVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
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
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $attachment->getRecord());

        if ($attachment->isDeleted()) {
            throw $this->createNotFoundException();
        }

        $filename = $attachment->getAbsolutePath($this->getParameter('files_path'));
        $response = new BinaryFileResponse($filename);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $attachment->getName());
        $response->setPrivate();

        return $response;
    }

    /**
     * Partial with record's attachments.
     *
     * @Action\Route("/partial/{id}", name="web_partial_attachments", requirements={"id"="\d+"})
     *
     * @param   Record $record
     *
     * @return  Response
     */
    public function partialAction(Record $record): Response
    {
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $record);

        $fileForm = $this->createForm(AttachFileForm::class, null, [
            'action' => $this->generateUrl('web_attach_file', ['id' => $record->getId()]),
        ]);

        return $this->render('web/records/_attachments.html.twig', [
            'record'   => $record,
            'fileForm' => $fileForm->createView(),
        ]);
    }
}
