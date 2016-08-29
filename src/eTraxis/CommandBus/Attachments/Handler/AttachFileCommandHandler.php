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
use eTraxis\CommandBus\Attachments\AttachFileCommand;
use eTraxis\Entity\Attachment;
use eTraxis\Entity\Record;
use eTraxis\Entity\User;
use eTraxis\Voter\RecordVoter;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Command handler.
 */
class AttachFileCommandHandler
{
    protected $translator;
    protected $manager;
    protected $security;
    protected $token_storage;
    protected $files_path;
    protected $files_max_size;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface           $translator
     * @param   EntityManagerInterface        $manager
     * @param   AuthorizationCheckerInterface $security
     * @param   TokenStorageInterface         $token_storage
     * @param   string                        $files_path
     * @param   string                        $files_max_size
     */
    public function __construct(
        TranslatorInterface           $translator,
        EntityManagerInterface        $manager,
        AuthorizationCheckerInterface $security,
        TokenStorageInterface         $token_storage,
        string                        $files_path,
        string                        $files_max_size)
    {
        $this->translator     = $translator;
        $this->manager        = $manager;
        $this->security       = $security;
        $this->token_storage  = $token_storage;
        $this->files_path     = $files_path;
        $this->files_max_size = $files_max_size;
    }

    /**
     * Attaches new file.
     *
     * @param   AttachFileCommand $command
     *
     * @throws  AccessDeniedHttpException
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(AttachFileCommand $command)
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

        if (!$this->security->isGranted(RecordVoter::ATTACH_FILE, $record)) {
            throw new AccessDeniedHttpException();
        }

        if ($command->file->getClientSize() > $this->files_max_size * 1024) {
            throw new BadRequestHttpException($this->translator->trans('error.file_max_size', ['%max_size%' => $this->files_max_size]));
        }

        $entity = new Attachment(
            $record,
            $user,
            $command->file->getClientOriginalName(),
            $command->file->getClientSize(),
            $command->file->getClientMimeType()
        );

        $filename = $entity->getAbsolutePath($this->files_path);
        $command->file->move(dirname($filename), $entity->getUuid());

        $this->manager->persist($entity);
    }
}
