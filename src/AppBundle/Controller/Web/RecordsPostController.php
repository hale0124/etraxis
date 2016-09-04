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

        $command = new Records\MarkRecordsAsReadCommand($data);
        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->markRecordsAsRead($this->getUser()->getId(), $command->records);

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

        $command = new Records\MarkRecordsAsUnreadCommand($data);
        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->markRecordsAsUnread($this->getUser()->getId(), $command->records);

        return new JsonResponse();
    }

    /**
     * Postpones specified record.
     *
     * @Action\Route("/postpone/{id}", name="web_postpone_record", requirements={"id"="\d+"})
     *
     * @param   int $id Record ID.
     *
     * @return  JsonResponse
     */
    public function postponeAction(int $id): JsonResponse
    {
        $command = new Records\PostponeCommand(['record' => $id]);
        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->markRecordsAsPostponed($this->getUser()->getId(), [$id]);

        return new JsonResponse();
    }

    /**
     * Resumes specified record.
     *
     * @Action\Route("/resume/{id}", name="web_resume_record", requirements={"id"="\d+"})
     *
     * @param   int $id Record ID.
     *
     * @return  JsonResponse
     */
    public function resumeAction(int $id): JsonResponse
    {
        $command = new Records\ResumeCommand(['record' => $id]);
        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->markRecordsAsNotPostponed($this->getUser()->getId(), [$id]);

        return new JsonResponse();
    }

    /**
     * Deletes specified record.
     *
     * @Action\Route("/delete/{id}", name="web_delete_record", requirements={"id"="\d+"})
     *
     * @param   int $id Record ID.
     *
     * @return  JsonResponse
     */
    public function deleteAction(int $id): JsonResponse
    {
        $command = new Records\DeleteCommand(['record' => $id]);
        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->deleteRecords($this->getUser()->getId());

        return new JsonResponse();
    }
}
