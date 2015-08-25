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

        if ($error = $utils->getLastAuthenticationError()) {
            $this->setError($error->getMessage());
        }

        return $this->render('web/security/login.html.twig', [
            'last_username' => $utils->getLastUsername(),
        ]);
    }

    /**
     * Forgot password page.
     *
     * @Action\Route("/forgot", name="forgot_password")
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

        $form = $this->createForm(new ForgotPasswordForm());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $command = new ForgotPasswordCommand([
                'username' => $form['username']->getData(),
                'ip'       => $request->getClientIp(),
            ]);

            $this->getCommandBus()->handle($command);

            $this->setNotice($this->getTranslator()->trans('security.forgot_password.email_sent'));

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('web/security/forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset password form.
     *
     * @Action\Route("/reset/{token}", name="reset_password")
     * @Action\Method({"GET", "POST"})
     *
     * @param   Request $request
     * @param   string  $token Reset password token.
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

        $form = $this->createForm(new ResetPasswordForm());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $this->getFormData($request, 'reset_password');

            if ($data['password'] != $data['confirmation']) {
                $this->setError($this->get('translator')->trans('passwords.dont_match'));
            }
            else {

                try {
                    $command = new ResetPasswordCommand([
                        'token'    => $token,
                        'password' => $data['password'],
                    ]);

                    $this->getCommandBus()->handle($command);

                    $this->setNotice($this->getTranslator()->trans('password.changed'));

                    return $this->redirect($this->generateUrl('login'));
                }
                catch (\Exception $e) {
                    $this->setError($e->getMessage());
                }
            }
        }

        return $this->render('web/security/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
