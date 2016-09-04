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
use eTraxis\CommandBus\Records\MarkRecordsAsUnreadCommand;
use eTraxis\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Command handler.
 */
class MarkRecordsAsUnreadCommandHandler
{
    protected $manager;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     * @param   TokenStorageInterface  $token_storage
     */
    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $token_storage)
    {
        $this->manager       = $manager;
        $this->token_storage = $token_storage;
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
        $token = $this->token_storage->getToken();

        if ($token === null) {
            throw new NotFoundHttpException('Unknown user.');
        }

        /** @var User $user */
        $user = $this->manager->find(User::class, $token->getUser()->getId());

        $query = $this->manager->createQuery('
            DELETE eTraxis:LastRead lastRead
            WHERE lastRead.user = :user
              AND lastRead.record IN (:records)
        ');

        $query->execute([
            'user'    => $user->getId(),
            'records' => $command->records,
        ]);
    }
}
