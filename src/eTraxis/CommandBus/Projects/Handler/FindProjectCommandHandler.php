<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Projects\Handler;

use eTraxis\CommandBus\Projects\FindProjectCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Command handler.
 */
class FindProjectCommandHandler
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
     * Finds specified project.
     *
     * @param   FindProjectCommand $command
     *
     * @return  \eTraxis\Entity\Project
     */
    public function handle(FindProjectCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:Project');

        return $repository->find($command->id);
    }
}
