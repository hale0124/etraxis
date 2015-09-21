<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\Controller\Web;

use eTraxis\CommandBus\Users;
use eTraxis\CommandBus\ValidationException;
use eTraxis\Form\AppearanceForm;
use eTraxis\Traits\ContainerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Action;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Settings controller.
 *
 * @Action\Route("/settings")
 */
class SettingsController extends Controller
{
    use ContainerTrait;

    /**
     * Page with user's settings.
     *
     * @Action\Route("/", name="settings")
     * @Action\Method("GET")
     *
     * @return  Response
     */
    public function indexAction()
    {
        $form = $this->createForm(new AppearanceForm($this->getTranslator()), $this->getUser(), [
            'action' => $this->generateUrl('settings_appearance'),
        ]);

        return $this->render('web/settings/index.html.twig', [
            'form' => $form->createView(),
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
     * @return  Response
     */
    public function appearanceAction(Request $request)
    {
        try {
            $data       = $this->getFormData($request, 'appearance');
            $data['id'] = $this->getUser()->getId();

            $command = new Users\SaveAppearanceCommand($data);
            $this->getCommandBus()->handle($command);

            $this->get('session')->set('_locale', $command->locale);

            $this->setNotice($this->getTranslator()->trans('changes.saved'));
        }
        catch (ValidationException $e) {
            foreach ($e->getMessages() as $message) {
                $this->setError($message);
            }
        }

        return $this->redirectToRoute('settings');
    }
}
