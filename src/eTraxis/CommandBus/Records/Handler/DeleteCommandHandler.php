<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Records\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Records\DeleteCommand;
use eTraxis\Entity\Record;
use eTraxis\Voter\RecordVoter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteCommandHandler
{
    protected $manager;
    protected $security;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     */
    public function __construct(EntityManagerInterface $manager, AuthorizationCheckerInterface $security)
    {
        $this->manager  = $manager;
        $this->security = $security;
    }

    /**
     * Deletes specified record.
     *
     * @param   DeleteCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteCommand $command)
    {
        /** @var Record $record */
        $record = $this->manager->find(Record::class, $command->record);

        if (!$record) {
            throw new NotFoundHttpException('Unknown record.');
        }

        if (!$this->security->isGranted(RecordVoter::DELETE, $record)) {
            throw new AccessDeniedHttpException();
        }

        $this->manager->remove($record);
    }
}
