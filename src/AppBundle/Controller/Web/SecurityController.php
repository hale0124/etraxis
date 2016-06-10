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

use eTraxis\Form\ForgotPasswordForm;
use eTraxis\Form\ResetPasswordForm;
use eTraxis\SimpleBus\Users\ForgotPasswordCommand;
use eTraxis\SimpleBus\Users\ResetPasswordCommand;
use eTraxis\SimpleBus\Users\SetPasswordCommand;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Traits\FlashBagTrait;
use eTraxis\Voter\UserVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Security controller.
 */
class SecurityController extends Controller
{
    use ContainerTrait;
    use FlashBagTrait;

    /**
     * Login page.
     *
     * @Action\Route("/login", name="login")
     * @Action\Method({"GET", "POST"})
     *
     * @return  Response
     */
    public function loginAction(): Response
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
     * @return  Response
     */
    public function forgotPasswordAction(Request $request): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createForm(ForgotPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $command = new ForgotPasswordCommand([
                'username' => $form['username']->getData(),
                'ip'       => $request->getClientIp(),
            ]);

            try {
                $this->getCommandBus()->handle($command);
                $this->setNotice($this->container->get('translator')->trans('security.forgot_password.email_sent'));

                return $this->redirect($this->generateUrl('homepage'));
            }
            catch (\Exception $e) {
                $this->setError($e->getMessage());
            }
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
     * @return  Response
     */
    public function resetPasswordAction(Request $request, string $token): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if ($security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createForm(ResetPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $request->request->get('reset_password');

            if ($data['password'] !== $data['confirmation']) {
                $this->setError($this->container->get('translator')->trans('passwords.dont_match'));
            }
            else {

                try {
                    $command = new ResetPasswordCommand([
                        'token'    => $token,
                        'password' => $data['password'],
                    ]);

                    $this->getCommandBus()->handle($command);

                    $this->setNotice($this->container->get('translator')->trans('password.changed'));

                    return $this->redirect($this->generateUrl('login'));
                }
                catch (HttpException $e) {
                    $this->setError($e->getMessage());
                }
            }
        }

        return $this->render('web/security/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Set expired password form.
     *
     * @Action\Route("/expired", name="set_expired_password")
     * @Action\Method({"GET", "POST"})
     *
     * @param   Request $request
     *
     * @return  Response
     */
    public function setExpiredPasswordAction(Request $request): Response
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationChecker $security */
        $security = $this->container->get('security.authorization_checker');

        if (!$security->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('login'));
        }

        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->get('security.authorization_checker');

        if (!$authChecker->isGranted(UserVoter::SET_EXPIRED_PASSWORD, $this->getUser())) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        $form = $this->createForm(ResetPasswordForm::class);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $request->request->get('reset_password');

            if ($data['password'] !== $data['confirmation']) {
                $this->setError($this->container->get('translator')->trans('passwords.dont_match'));
            }
            else {

                try {
                    $command = new SetPasswordCommand([
                        'id'       => $this->getUser()->getId(),
                        'password' => $data['password'],
                    ]);

                    $this->getCommandBus()->handle($command);

                    $this->setNotice($this->container->get('translator')->trans('password.changed'));

                    return $this->redirect($this->generateUrl('homepage'));
                }
                catch (HttpException $e) {
                    $this->setError($e->getMessage());
                }
            }
        }
        else {
            $this->setNotice('security.password_expired');
        }

        return $this->render('web/security/reset.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
