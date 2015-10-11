<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use eTraxis\CommandBus\Groups\FindGroupCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class FindGroupCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Finds specified group.
     *
     * @param   FindGroupCommand $command
     *
     * @return  \eTraxis\Entity\Group
     */
    public function handle(FindGroupCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:Group');

        return $repository->find($command->id);
    }
}
