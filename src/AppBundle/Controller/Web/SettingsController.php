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

use eTraxis\Form\AppearanceForm;
use eTraxis\Form\ChangePasswordForm;
use eTraxis\SimpleBus\Users;
use eTraxis\Traits\ContainerTrait;
use eTraxis\Traits\FlashBagTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use SimpleBus\ValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Settings controller.
 *
 * @Action\Route("/settings")
 */
class SettingsController extends Controller
{
    use ContainerTrait;
    use FlashBagTrait;

    /**
     * Page with user's settings.
     *
     * @Action\Route("/", name="settings")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function indexAction(): Response
    {
        $appearance_form = $this->createForm(AppearanceForm::class, $this->getUser(), [
            'action' => $this->generateUrl('settings_appearance'),
        ]);

        $password_form = $this->createForm(ChangePasswordForm::class, $this->getUser(), [
            'action' => $this->generateUrl('settings_password'),
        ]);

        return $this->render('web/settings/index.html.twig', [
            'appearance_form' => $appearance_form->createView(),
            'password_form'   => $password_form->createView(),
        ]);
    }

    /**
     * Processes submitted form when user's appearance is being saved.
     *
     * @Action\Route("/appearance", name="settings_appearance")
     * @Action\Method("POST")
     *
     * @param   Request $request
     *
     * @return  RedirectResponse
     */
    public function appearanceAction(Request $request): RedirectResponse
    {
        try {
            $data = $request->request->get('appearance');

            $command = new Users\SaveAppearanceCommand($data ?? [], ['id' => $this->getUser()->getId()]);
            $this->getCommandBus()->handle($command);

            $this->get('session')->set('_locale', $command->locale);

            $this->setNotice($this->container->get('translator')->trans('changes_saved', [], null, $command->locale));
        }
        catch (ValidationException $e) {
            foreach ($e->getMessages() as $message) {
                $this->setError($message);
            }
        }
        catch (HttpException $e) {
            $this->setError($e->getMessage());
        }

        return $this->redirectToRoute('settings');
    }

    /**
     * Processes submitted form when user changes his password.
     *
     * @Action\Route("/password", name="settings_password")
     * @Action\Method("POST")
     *
     * @param   Request $request
     *
     * @return  RedirectResponse
     */
    public function passwordAction(Request $request): RedirectResponse
    {
        /** @var \eTraxis\Entity\User $user */
        $user = $this->getUser();

        try {

            if ($user->isLdap()) {
                throw new BadRequestHttpException($this->container->get('translator')->trans('password.cant_change'));
            }

            $data = $request->request->get('change_password');

            /** @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
            $encoder = $this->get('etraxis.encoder');

            if (!$encoder->isPasswordValid($user->getPassword(), $data['current_password'], null)) {
                throw new BadRequestHttpException($this->container->get('translator')->trans('password.wrong'));
            }

            if ($data['new_password'] !== $data['confirmation']) {
                throw new BadRequestHttpException($this->container->get('translator')->trans('passwords.dont_match'));
            }

            $command = new Users\SetPasswordCommand([
                'id'       => $user->getId(),
                'password' => $data['new_password'],
            ]);

            $this->getCommandBus()->handle($command);

            $this->setNotice($this->container->get('translator')->trans('password.changed'));
        }
        catch (BadCredentialsException $e) {
            $this->setError($this->container->get('translator')->trans('password.wrong'));
        }
        catch (ValidationException $e) {
            foreach ($e->getMessages() as $message) {
                $this->setError($message);
            }
        }
        catch (HttpException $e) {
            $this->setError($e->getMessage());
        }

        return $this->redirectToRoute('settings');
    }
}
