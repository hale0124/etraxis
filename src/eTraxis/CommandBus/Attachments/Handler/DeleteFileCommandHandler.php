<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Attachments\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Attachments\DeleteFileCommand;
use eTraxis\Dictionary\EventType;
use eTraxis\Entity\Attachment;
use eTraxis\Entity\Event;
use eTraxis\Entity\User;
use eTraxis\Voter\RecordVoter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Command handler.
 */
class DeleteFileCommandHandler
{
    protected $manager;
    protected $security;
    protected $token_storage;
    protected $files_path;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     * @param   TokenStorageInterface         $token_storage
     * @param   string                        $files_path
     */
    public function __construct(
        EntityManagerInterface        $manager,
        AuthorizationCheckerInterface $security,
        TokenStorageInterface         $token_storage,
        string                        $files_path)
    {
        $this->manager       = $manager;
        $this->security      = $security;
        $this->token_storage = $token_storage;
        $this->files_path    = $files_path;
    }

    /**
     * Deletes specified file.
     *
     * @param   DeleteFileCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(DeleteFileCommand $command)
    {
        $token = $this->token_storage->getToken();

        if ($token === null) {
            throw new NotFoundHttpException('Unknown user.');
        }

        /** @var User $user */
        $user = $this->manager->find(User::class, $token->getUser()->getId());

        /** @var Attachment $entity */
        $entity = $this->manager->find(Attachment::class, $command->id);

        if (!$entity || $entity->isDeleted()) {
            throw new NotFoundHttpException('Unknown file.');
        }

        $record = $entity->getRecord();

        if (!$this->security->isGranted(RecordVoter::DELETE_FILE, $record)) {
            throw new AccessDeniedHttpException();
        }

        $event = new Event($record, $user, EventType::FILE_DELETED, $entity->getId());

        $filename = $entity->getAbsolutePath($this->files_path);

        if (file_exists($filename)) {
            unlink($filename);
        }

        $entity->setDeleted(true);

        $this->manager->persist($event);
        $this->manager->persist($entity);
    }
}
