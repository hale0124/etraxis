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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Group;
use eTraxis\SimpleBus\Groups\DeleteGroupCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class DeleteGroupCommandHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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
        $entity = $this->manager->find(Group::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown group.');
        }

        $this->manager->remove($entity);
        $this->manager->flush();
    }
}
