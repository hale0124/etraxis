<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups\Handler;

use eTraxis\Entity\Group;
use eTraxis\SimpleBus\Groups\DeleteGroupCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class DeleteGroupCommandHandler
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
     * Deletes specified group.
     *
     * @param   DeleteGroupCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteGroupCommand $command)
    {
        $repository = $this->doctrine->getRepository(Group::class);

        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown group.');
        }

        $this->doctrine->getManager()->remove($entity);
        $this->doctrine->getManager()->flush();
    }
}
