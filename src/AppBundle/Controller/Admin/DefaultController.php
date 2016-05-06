<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for admin area.
 */
class DefaultController extends Controller
{
    /**
     * @Action\Route("/", name="admin")
     * @Action\Method("GET")
     */
    public function indexAction(): Response
    {
        return $this->redirect($this->generateUrl('admin_users'));
    }
}
