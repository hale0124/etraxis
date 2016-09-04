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
use eTraxis\CommandBus\Records\MarkRecordsAsReadCommand;
use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Command handler.
 */
class MarkRecordsAsReadCommandHandler
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
     * Marks specified records as read.
     *
     * @param   MarkRecordsAsReadCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(MarkRecordsAsReadCommand $command)
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

        $query = $this->manager->createQueryBuilder();

        $query
            ->select('r')
            ->from(Record::class, 'r')
            ->where($query->expr()->in('r.id', ':records'))
            ->setParameter('records', $command->records)
        ;

        /** @var Record[] $records */
        $records = $query->getQuery()->getResult();

        foreach ($records as $record) {
            $lastRead = new LastRead($record, $user);
            $this->manager->persist($lastRead);
        }
    }
}
