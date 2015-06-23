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

/**
 * A trait to access known services from DI container.
 *
 * @property \Symfony\Component\DependencyInjection\ContainerInterface $container
 */
trait ContainerTrait
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
     * Shortcut to get the Command Bus service.
     *
     * @return  \SimpleBus\Message\Bus\MessageBus
     */
    protected function getCommandBus()
    {
        return $this->container->get('command_bus');
    }
}
