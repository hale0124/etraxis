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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

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
     * @param   Request $request
     *
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $session = $request->getSession();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        }
        elseif ($session && $session->has(Security::AUTHENTICATION_ERROR)) {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }
        else {
            $error = null;
        }

        return $this->render('web/security/login.html.twig', [
            'last_username' => $session ? $session->get(Security::LAST_USERNAME) : null,
            'error'         => $error,
        ]);
    }
}
