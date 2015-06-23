<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use eTraxis\Exception\ResponseException;
use eTraxis\SimpleBus\Command\User\ListUsersCommand;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Users controller.
 *
 * @Route("/users")
 */
class UsersController extends Controller
{
    use ContainerTrait;

    /**
     * Page with list of users.
     *
     * @Route("/", name="admin_users")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/users/index.html.twig');
    }

    /**
     * Returns JSON list of users for DataTables
     * (see http://datatables.net/manual/server-side for details).
     *
     * @Route("/ajax", name="admin_users_ajax")
     * @Method("GET")
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function ajaxAction(Request $request)
    {
        $command = new ListUsersCommand();

        $search = $request->get('search', ['value' => null]);

        $command->start  = $request->get('start', 0);
        $command->length = $request->get('length', -1);
        $command->search = $search['value'];
        $command->order  = $request->get('order', []);

        try {
            $this->getCommandBus()->handle($command);
        }
        catch (ResponseException $e) {
            $this->getLogger()->error($e->getMessage(), [$e->getCode()]);

            return new Response($e->getMessage(), $e->getCode());
        }

        return new JsonResponse([
            'draw'            => $request->get('draw'),
            'recordsTotal'    => $command->total,
            'recordsFiltered' => $command->total,
            'data'            => $command->users,
        ]);
    }
}
