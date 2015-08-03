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

namespace AppBundle\Controller\Web;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Default controller for public area.
 */
class DefaultController extends Controller
{
    /**
     * @Action\Route("/", name="homepage")
     * @Action\Method("GET")
     */
    public function indexAction()
    {
        return $this->render('web/base.html.twig');
    }
}
