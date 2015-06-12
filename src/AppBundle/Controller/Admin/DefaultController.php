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

use AppBundle\Controller\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Default controller for admin area.
 */
class DefaultController extends BaseController
{
    /**
     * @Route("/", name="admin")
     * @Method("GET")
     */
    public function indexAction()
    {
        return $this->render('admin/base.html.twig');
    }
}
