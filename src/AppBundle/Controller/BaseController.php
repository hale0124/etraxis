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


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Extends classic Symfony Controller with several shortcut functions.
 */
class BaseController extends Controller
{
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
     * Shortcut to get the Security service.
     *
     * @return  \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected function getSecurityContext()
    {
        return $this->container->get('security.context');
    }

    /**
     * Shortcut to get the Session service.
     *
     * @return  \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * Shortcut to get the Translator service.
     *
     * @return  \Symfony\Component\Translation\TranslatorInterface
     */
    protected function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * Shortcut to get the Validator service.
     *
     * @return  \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected function getValidator()
    {
        return $this->container->get('validator');
    }

    /**
     * Shortcut to get the Object Manager.
     *
     * @return  \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getObjectManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Shortcut to get the Twig service.
     *
     * @return  \Twig_Environment
     */
    protected function getTwig()
    {
        return $this->container->get('twig');
    }

    /**
     * Shortcut to get the mailer service.
     *
     * @return  \eTraxis\Service\MailerService
     */
    protected function getMailer()
    {
        return $this->container->get('etraxis.mailer');
    }
}
