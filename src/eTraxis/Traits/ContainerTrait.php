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

namespace eTraxis\Traits;

use Symfony\Component\Form\Form;

/**
 * A trait to access known services from DI container.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait ContainerTrait
{
    /**
     * Returns formatted message for first error in specified form.
     *
     * @param   Form $form Submitted form.
     *
     * @return  string
     */
    protected function getFormError(Form $form)
    {
        $errors = $form->getErrors(true);

        if (count($errors) == 0) {
            return '';
        }

        $option  = $errors[0]->getOrigin()->getConfig()->getOption('label');
        $message = $errors[0]->getMessage();

        if ($option) {
            /** @var \Symfony\Component\Translation\TranslatorInterface $translator */
            $translator = $this->container->get('translator');

            $message = '<p class="field-error">' . $translator->trans($option) . '</p>' . $message;
        }

        return $message;
    }

    /**
     * Shortcut to get the Logger service.
     *
     * @return  \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * Shortcut to get the Command Bus service.
     *
     * @return  \SimpleBus\Message\Bus\MessageBus
     */
    protected function getCommandBus()
    {
        return $this->container->get('command_bus');
    }
}
