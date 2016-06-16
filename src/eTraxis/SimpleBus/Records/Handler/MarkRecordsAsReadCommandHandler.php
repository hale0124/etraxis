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
use eTraxis\Entity\LastRead;
use eTraxis\Entity\Record;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Records\MarkRecordsAsReadCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class MarkRecordsAsReadCommandHandler
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
     * Marks specified records as read.
     *
     * @param   MarkRecordsAsReadCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(MarkRecordsAsReadCommand $command)
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
