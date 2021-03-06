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

use eTraxis\CommandBus\Middleware\ValidationException;
use eTraxis\CommandBus\Records\MarkRecordsAsReadCommand;
use eTraxis\Dictionary\EventType;
use eTraxis\Entity\Record;
use eTraxis\Form\AttachFileForm;
use eTraxis\Form\CommentForm;
use eTraxis\Service\Export\ExportCsvQuery;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Voter\RecordVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Records "GET" controller.
 *
 * @Action\Route("/records", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class RecordsGetController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of records.
     *
     * @Action\Route("/", name="web_records", condition="")
     *
     * @return  Response
     */
    public function indexAction(): Response
    {
        return $this->render('web/records/index.html.twig');
    }

    /**
     * Returns JSON list of records for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Action\Route("/list", name="web_records_list")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function listAction(Request $request): JsonResponse
    {
        /** @var \DataTables\DataTablesInterface $datatables */
        $datatables = $this->container->get('datatables');
        $results    = $datatables->handle($request, 'eTraxis:Record');

        return new JsonResponse($results);
    }

    /**
     * Exports list of records as CSV file.
     *
     * @Action\Route("/csv", name="web_records_csv", condition="")
     *
     * @param   Request $request
     *
     * @return  StreamedResponse
     */
    public function csvAction(Request $request): StreamedResponse
    {
        $request->query->set('start', 0);
        $request->query->set('length', -1);

        /** @var \DataTables\DataTablesInterface $datatables */
        $datatables = $this->container->get('datatables');
        $results    = $datatables->handle($request, 'eTraxis:Record');

        $records = array_map(function ($record) {
            return array_slice($record, 1, 7);
        }, $results->data);

        /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
        $translator = $this->container->get('translator');

        array_unshift($records, [
            $translator->trans('record.id'),
            $translator->trans('project'),
            $translator->trans('state'),
            $translator->trans('record.subject'),
            $translator->trans('role.author'),
            $translator->trans('role.responsible'),
            $translator->trans('record.age'),
        ]);

        $query = new ExportCsvQuery($request->query->get('export'));

        /** @var \Symfony\Component\Validator\ConstraintViolationInterface[] $violations */
        $violations = $this->get('validator')->validate($query);

        if (count($violations)) {
            throw new ValidationException($violations);
        }

        /** @var \eTraxis\Service\ExportInterface $export */
        $export = $this->get('etraxis.export');

        return $export->exportCsv($query, $records);
    }

    /**
     * Shows specified record.
     *
     * @Action\Route("/{id}", name="web_view_record", condition="", requirements={"id"="\d+"})
     *
     * @param   Request $request
     * @param   Record  $record
     *
     * @return  Response
     */
    public function viewAction(Request $request, Record $record): Response
    {
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $record);

        $command = new MarkRecordsAsReadCommand([
            'user'    => $this->getUser()->getId(),
            'records' => [$record->getId()],
        ]);

        $this->getCommandBus()->handle($command);

        /** @var \eTraxis\Service\RecordsCacheInterface $cache */
        $cache = $this->get('etraxis.records_cache');
        $cache->markRecordsAsRead($this->getUser()->getId(), $command->records);

        return $this->render('web/records/view.html.twig', [
            'record' => $record,
            'tab'    => $request->get('tab', 0),
        ]);
    }

    /**
     * Tab with record's details.
     *
     * @Action\Route("/tab/details/{id}", name="web_tab_record_details", requirements={"id"="\d+"})
     *
     * @param   Record $record
     *
     * @return  Response
     */
    public function tabDetailsAction(Record $record): Response
    {
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $record);

        /** @var \eTraxis\Security\CurrentUser $user */
        $user = $this->getUser();

        /** @var \eTraxis\Service\RecordsCache\RecordsCacheService $cache */
        $cache = $this->container->get('etraxis.records_cache');

        $commentForm = $this->createForm(CommentForm::class, null, [
            'action' => $this->generateUrl('web_new_comment', ['id' => $record->getId()]),
        ]);

        $fileForm = $this->createForm(AttachFileForm::class, null, [
            'action' => $this->generateUrl('web_attach_file', ['id' => $record->getId()]),
        ]);

        return $this->render('web/records/tab_details.html.twig', [
            'record'      => $record,
            'previous'    => $cache->getPrevious($user->getId(), $record->getId()),
            'next'        => $cache->getNext($user->getId(), $record->getId()),
            'commentForm' => $commentForm->createView(),
            'fileForm'    => $fileForm->createView(),
        ]);
    }

    /**
     * Tab with record's history.
     *
     * @Action\Route("/tab/history/{id}", name="web_tab_record_history", requirements={"id"="\d+"})
     *
     * @param   Record $record
     *
     * @return  Response
     */
    public function tabHistoryAction(Record $record): Response
    {
        $this->denyAccessUnlessGranted(RecordVoter::VIEW, $record);

        return $this->render('web/records/tab_history.html.twig', [
            'record' => $record,
            'types'  => EventType::all(),
        ]);
    }
}
