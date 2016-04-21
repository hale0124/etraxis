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

use eTraxis\Dictionary;
use eTraxis\Form\ExportCsvForm;
use eTraxis\Service\Export\ExportCsvQuery;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for public area.
 */
class DefaultController extends Controller
{
    use ContainerTrait;

    /**
     * @Action\Route("/", name="homepage")
     * @Action\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('web/base.html.twig');
    }

    /**
     * Renders dialog for export something into CSV.
     *
     * @Action\Route("/dlg/export", name="dlg_export", condition="request.isXmlHttpRequest()")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function dlgExportAction()
    {
        $default = [
            'filename'  => '.csv',
            'delimiter' => Dictionary\CsvDelimiter::FALLBACK,
            'encoding'  => Dictionary\Encoding::FALLBACK,
            'tail'      => Dictionary\LineEnding::FALLBACK,
        ];

        $form = $this->createForm(ExportCsvForm::class, $default, [
            'action' => $this->generateUrl('export'),
        ]);

        return $this->render('web/default/dlg_export.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Verifies submitted form of "Export to CSV" parameters.
     *
     * @Action\Route("/export", name="export", condition="request.isXmlHttpRequest()")
     * @Action\Method("POST")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function exportAction(Request $request)
    {
        $query = new ExportCsvQuery($request->request->get('export'));

        /** @var \Symfony\Component\Validator\ConstraintViolationListInterface $violations */
        $violations = $this->get('validator')->validate($query);

        if (count($violations)) {
            return new JsonResponse($violations->get(0)->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse();
    }
}
