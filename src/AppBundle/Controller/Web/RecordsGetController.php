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

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records "GET" controller.
 *
 * @Action\Route("/records", condition="request.isXmlHttpRequest()")
 * @Action\Method("GET")
 */
class RecordsGetController extends Controller
{
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
}
