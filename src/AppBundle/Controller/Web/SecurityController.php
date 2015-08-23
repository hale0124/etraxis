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

use eTraxis\CommandBus\Users\ForgotPasswordCommand;
use eTraxis\CommandBus\Users\ResetPasswordCommand;
use eTraxis\Form\ForgotPasswordForm;
use eTraxis\Form\ResetPasswordForm;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Security controller.
 */
class SecurityController extends Controller
{
    use ContainerTrait;

    /**
     * Login page.
     *
     * @Action\Route("/login", name="login")
     * @Action\Method({"GET", "POST"})
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

    /**
     * Forgot password page.
     *
     * @Action\Route("/forgot", name="forgot")
     * @Action\Method({"GET", "POST"})
     *
     * @param   Request $request
     *
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPasswordAction(Request $request)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $message = null;

        $form = $this->createForm(new ForgotPasswordForm());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $command = new ForgotPasswordCommand([
                'username' => $form['username']->getData(),
                'ip'       => $request->getClientIp(),
            ]);

            $this->getCommandBus()->handle($command);

            $message = $this->getTranslator()->trans('security.forgot_password.email_sent');
        }

        return $this->render('web/security/forgot.html.twig', [
            'form'    => $form->createView(),
            'message' => $message,
        ]);
    }

    /**
     * Reset password form.
     *
     * @Action\Route("/reset/{token}", name="reset")
     * @Action\Method({"GET", "POST"})
     *
     * @param   Request $request
     * @param   string  $token Reser password token.
     *
     * @return  \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request, $token)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $message = null;
        $error   = null;

        $form = $this->createForm(new ResetPasswordForm());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $this->getFormData($request, 'reset_password');

            if ($data['password'] != $data['confirmation']) {
                $error = $this->get('translator')->trans('passwords.dont_match');
            }
            else {

                try {
                    $command = new ResetPasswordCommand([
                        'token'    => $token,
                        'password' => $data['password'],
                    ]);

                    $this->getCommandBus()->handle($command);

                    $message = $this->getTranslator()->trans('password.changed');
                }
                catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        return $this->render('web/security/reset.html.twig', [
            'form'    => $form->createView(),
            'message' => $message,
            'error'   => $error,
        ]);
    }
}
