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
use eTraxis\CommandBus\Records\PostponeCommand;
use eTraxis\Entity\Record;
use eTraxis\Entity\User;
use eTraxis\Voter\RecordVoter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class PostponeCommandHandler
{
    protected $manager;
    protected $security;
    protected $token_storage;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     * @param   TokenStorageInterface         $token_storage
     */
    public function __construct(
        EntityManagerInterface        $manager,
        AuthorizationCheckerInterface $security,
        TokenStorageInterface         $token_storage)
    {
        $this->manager       = $manager;
        $this->security      = $security;
        $this->token_storage = $token_storage;
    }

    /**
     * Postpones specified record.
     *
     * @param   PostponeCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(PostponeCommand $command)
    {
        $token = $this->token_storage->getToken();

        if ($token === null) {
            throw new NotFoundHttpException('Unknown user.');
        }

        /** @var User $user */
        $user = $this->manager->find(User::class, $token->getUser()->getId());

        /** @var Record $record */
        $record = $this->manager->find(Record::class, $command->record);

        if (!$record) {
            throw new NotFoundHttpException('Unknown record.');
        }

        if (!$this->security->isGranted(RecordVoter::POSTPONE, $record)) {
            throw new AccessDeniedHttpException();
        }

        $record->postpone($user);

        $this->manager->persist($record);
    }
}
