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

use League\Tactician\CommandBus;

/**
 * A trait to extend standard controller class.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait ContainerTrait
{
    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param   mixed $attributes
     * @param   mixed $object
     *
     * @return  bool
     */
    protected function isGranted($attributes, $object = null)
    {
        /** @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authChecker */
        $authChecker = $this->container->get('security.authorization_checker');

        return $authChecker->isGranted($attributes, $object);
    }

    /**
     * Shortcut to get the Command Bus service.
     *
     * @return  CommandBus
     */
    protected function getCommandBus(): CommandBus
    {
        return $this->container->get('tactician.commandbus');
    }
}
