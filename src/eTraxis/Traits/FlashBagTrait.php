<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Traits;

/**
 * A trait with shortcuts to the FlashBag.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait FlashBagTrait
{
    /**
     * Sets information message to be displayed on the next page.
     *
     * @param   string $notice Message.
     */
    protected function setNotice($notice)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->add('notice', $notice);
    }

    /**
     * Sets error message to be displayed on the next page.
     *
     * @param   string $error Message.
     */
    protected function setError($error)
    {
        /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface $flashBag */
        $flashBag = $this->container->get('session')->getFlashBag();
        $flashBag->add('error', $error);
    }
}
