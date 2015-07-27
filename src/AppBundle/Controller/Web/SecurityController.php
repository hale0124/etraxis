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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Security controller.
 */
class SecurityController extends Controller
{
    /**
     * Login page.
     *
     * @Route("/login", name="login")
     * @Method({"GET", "POST"})
     *
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $utils */
        $utils = $this->get('security.authentication_utils');

        return $this->render('web/security/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error'         => $utils->getLastAuthenticationError(),
        ]);
    }
}
