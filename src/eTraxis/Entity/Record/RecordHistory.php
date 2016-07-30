<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Record;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Dictionary\EventType;
use eTraxis\Entity\Attachment;
use eTraxis\Entity\Event;
use eTraxis\Entity\State;
use eTraxis\Entity\User;

/**
 * Record's history.
 */
class RecordHistory extends \ArrayIterator
{
    /**
     * Constructor.
     *
     * @param   ArrayCollection|Event[] $events  Original events from the record.
     * @param   EntityManagerInterface  $manager Entity manager.
     */
    public function __construct(ArrayCollection $events, EntityManagerInterface $manager)
    {
        $users    = $this->getHistoryUsers($events, $manager->getRepository(User::class));
        $states   = $this->getHistoryStates($events, $manager->getRepository(State::class));
        $attached = $this->getHistoryAttachedFiles($events, $manager->getRepository(Attachment::class));
        $deleted  = $this->getHistoryDeletedFiles($events, $manager->getRepository(Attachment::class));

        $recordEvents = $events->map(function (Event $event) use ($users, $states, $attached, $deleted) {

            switch ($event->getType()) {

                case EventType::RECORD_ASSIGNED:
                    $parameter = $users[$event->getParameter()]->getFullname();
                    break;

                case EventType::RECORD_CREATED:
                case EventType::RECORD_REOPENED:
                case EventType::STATE_CHANGED:
                    $parameter = $states[$event->getParameter()]->getName();
                    break;

                case EventType::FILE_ATTACHED:
                    $parameter = $attached[$event->getId()]->getName();
                    break;

                case EventType::FILE_DELETED:
                    $parameter = $deleted[$event->getParameter()]->getName();
                    break;

                default:
                    $parameter = $event->getParameter();
            }

            return new RecordEvent(
                $event->getUser(),
                $event->getType(),
                $event->getCreatedAt(),
                $parameter
            );
        });

        parent::__construct($recordEvents->toArray());
    }

    /**
     * Returns all users mentioned in the record's history.
     *
     * @param   ArrayCollection|Event[] $events     Original events from the record.
     * @param   ObjectRepository        $repository Users repository.
     *
     * @return  User[]
     */
    protected function getHistoryUsers(ArrayCollection $events, ObjectRepository $repository)
    {
        $ids = [];

        foreach ($events as $event) {
            if ($event->getType() === EventType::RECORD_ASSIGNED) {
                $ids[] = $event->getParameter();
            }
        }

        /** @var User[] $users */
        $users = $repository->findBy(['id' => $ids], ['id' => 'ASC']);

        $result = [];

        foreach ($users as $user) {
            $result[$user->getId()] = $user;
        }

        return $result;
    }

    /**
     * Returns all states mentioned in the record's history.
     *
     * @param   ArrayCollection|Event[] $events     Original events from the record.
     * @param   ObjectRepository        $repository States repository.
     *
     * @return  State[]
     */
    protected function getHistoryStates(ArrayCollection $events, ObjectRepository $repository)
    {
        $ids = [];

        foreach ($events as $event) {
            if ($event->getType() === EventType::RECORD_CREATED ||
                $event->getType() === EventType::RECORD_REOPENED ||
                $event->getType() === EventType::STATE_CHANGED)
            {
                $ids[] = $event->getParameter();
            }
        }

        /** @var State[] $states */
        $states = $repository->findBy(['id' => $ids], ['id' => 'ASC']);

        $result = [];

        foreach ($states as $state) {
            $result[$state->getId()] = $state;
        }

        return $result;
    }

    /**
     * Returns all attachments mentioned in the record's history.
     *
     * @param   ArrayCollection|Event[] $events     Original events from the record.
     * @param   ObjectRepository        $repository Attachments repository.
     *
     * @return  Attachment[]
     */
    protected function getHistoryAttachedFiles(ArrayCollection $events, ObjectRepository $repository)
    {
        $ids = [];

        foreach ($events as $event) {
            if ($event->getType() === EventType::FILE_ATTACHED) {
                $ids[] = $event->getId();
            }
        }

        /** @var Attachment[] $attachments */
        $attachments = $repository->findBy(['event' => $ids], ['event' => 'ASC']);

        $result = [];

        foreach ($attachments as $attachment) {
            $result[$attachment->getEvent()->getId()] = $attachment;
        }

        return $result;
    }

    /**
     * Returns all deleted attachments mentioned in the record's history.
     *
     * @param   ArrayCollection|Event[] $events     Original events from the record.
     * @param   ObjectRepository        $repository Attachments repository.
     *
     * @return  Attachment[]
     */
    protected function getHistoryDeletedFiles(ArrayCollection $events, ObjectRepository $repository)
    {
        $ids = [];

        foreach ($events as $event) {
            if ($event->getType() === EventType::FILE_DELETED) {
                $ids[] = $event->getParameter();
            }
        }

        $result = [];

        /** @var Attachment[] $attachments */
        $attachments = $repository->findBy(['id' => $ids], ['id' => 'ASC']);

        foreach ($attachments as $attachment) {
            $result[$attachment->getId()] = $attachment;
        }

        return $result;
    }
}
