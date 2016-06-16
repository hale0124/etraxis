<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Records\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Records\MarkRecordsAsUnreadCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class MarkRecordsAsUnreadCommandHandler
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
     * Marks specified records as unread.
     *
     * @param   MarkRecordsAsUnreadCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(MarkRecordsAsUnreadCommand $command)
    {
        /** @var User $user */
        $user = $this->manager->find(User::class, $command->user);

        if (!$user) {
            throw new NotFoundHttpException('Unknown user.');
        }

        $query = $this->manager->createQuery('
            DELETE eTraxis:LastRead lastRead
            WHERE lastRead.user = :user
              AND lastRead.record IN (:records)
        ');

        $query->execute([
            'user'    => $command->user,
            'records' => $command->records,
        ]);
    }
}
