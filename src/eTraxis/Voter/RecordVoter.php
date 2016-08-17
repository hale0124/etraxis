<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Dictionary\TemplatePermission;
use eTraxis\Entity\Record;
use eTraxis\Security\CurrentUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Record" objects.
 */
class RecordVoter extends Voter
{
    const VIEW             = 'record.view';
    const EDIT             = 'record.edit';
    const DELETE           = 'record.delete';
    const REASSIGN         = 'record.reassign';
    const REOPEN           = 'record.reopen';
    const POSTPONE         = 'record.postpone';
    const RESUME           = 'record.resume';
    const PUBLIC_COMMENT   = 'record.public_comment';
    const PRIVATE_COMMENT  = 'record.private_comment';
    const ATTACH_FILE      = 'record.attach_file';
    const DELETE_FILE      = 'record.delete_file';
    const ATTACH_SUBRECORD = 'record.attach_subrecord';
    const DETACH_SUBRECORD = 'record.detach_subrecord';

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
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attributes = [
            self::VIEW,
            self::EDIT,
            self::DELETE,
            self::REASSIGN,
            self::REOPEN,
            self::POSTPONE,
            self::RESUME,
            self::PUBLIC_COMMENT,
            self::PRIVATE_COMMENT,
            self::ATTACH_FILE,
            self::DELETE_FILE,
            self::ATTACH_SUBRECORD,
            self::DETACH_SUBRECORD,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof Record;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // User must be logged in.
        if (!$token->getUser() instanceof CurrentUser) {
            return false;
        }

        /** @var Record $subject */
        switch ($attribute) {

            case self::VIEW:
                return $this->isViewGranted($subject, $token->getUser());

            case self::EDIT:
                return $this->isEditGranted($subject, $token->getUser());

            case self::DELETE:
                return $this->isDeleteGranted($subject, $token->getUser());

            case self::REASSIGN:
                return $this->isReassignGranted($subject, $token->getUser());

            case self::REOPEN:
                return $this->isReopenGranted($subject, $token->getUser());

            case self::POSTPONE:
                return $this->isPostponeGranted($subject, $token->getUser());

            case self::RESUME:
                return $this->isResumeGranted($subject, $token->getUser());

            case self::PUBLIC_COMMENT:
                return $this->isPublicCommentsGranted($subject, $token->getUser());

            case self::PRIVATE_COMMENT:
                return $this->isPrivateCommentsGranted($subject, $token->getUser());

            case self::ATTACH_FILE:
                return $this->isAttachFileGranted($subject, $token->getUser());

            case self::DELETE_FILE:
                return $this->isDeleteFileGranted($subject, $token->getUser());

            case self::ATTACH_SUBRECORD:
                return $this->isAttachSubrecordGranted($subject, $token->getUser());

            case self::DETACH_SUBRECORD:
                return $this->isDetachSubrecordGranted($subject, $token->getUser());

            default:
                return false;
        }
    }

    /**
     * Checks whether user has specified permission depending on status of project, template and record.
     *
     * @param   Record      $subject    Record.
     * @param   CurrentUser $user       Current user.
     * @param   string      $permission Template permission.
     *
     * @return  bool
     */
    protected function isPermissionGranted(Record $subject, CurrentUser $user, string $permission): bool
    {
        // Check whether the record is frozen.
        if ($subject->isFrozen()) {
            return false;
        }

        // Check whether the template is locked.
        if ($subject->getTemplate()->isLocked()) {
            return false;
        }

        // Check whether the project is suspended.
        if ($subject->getProject()->isSuspended()) {
            return false;
        }

        // Check whether anyone is granted specified permission.
        if ($subject->getTemplate()->isRoleGranted(SystemRole::ANYONE, $permission)) {
            return true;
        }

        // Check whether author is granted specified permission.
        if ($subject->getAuthor()->getId() === $user->getId()) {
            if ($subject->getTemplate()->isRoleGranted(SystemRole::AUTHOR, $permission)) {
                return true;
            }
        }

        // Check whether responsible is granted specified permission.
        if ($subject->isAssigned() && $subject->getResponsible()->getId() === $user->getId()) {
            if ($subject->getTemplate()->isRoleGranted(SystemRole::RESPONSIBLE, $permission)) {
                return true;
            }
        }

        // Check whether current user belongs to any group which is granted specified permission.
        return $subject->getTemplate()->isUserGranted($user, $permission);
    }

    /**
     * Checks whether user can view the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isViewGranted(Record $subject, CurrentUser $user): bool
    {
        // Author always has view access.
        if ($subject->getAuthor()->getId() === $user->getId()) {
            return true;
        }

        // Responsible always has view access.
        if ($subject->isAssigned() && $subject->getResponsible()->getId() === $user->getId()) {
            return true;
        }

        // Check whether anyone is granted to view the record.
        if ($subject->getTemplate()->isRoleGranted(SystemRole::ANYONE, TemplatePermission::VIEW_RECORDS)) {
            return true;
        }

        // Check whether current user belongs to any group which is granted to view the record.
        return $subject->getTemplate()->isUserGranted($user, TemplatePermission::VIEW_RECORDS);
    }

    /**
     * Checks whether user can edit the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isEditGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::EDIT_RECORDS);
    }

    /**
     * Checks whether user can delete the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isDeleteGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::DELETE_RECORDS);
    }

    /**
     * Checks whether user can reassign the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isReassignGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is not assigned.
        if (!$subject->isAssigned()) {
            return false;
        }

        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::REASSIGN_RECORDS);
    }

    /**
     * Checks whether user can reopen the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isReopenGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is opened.
        if (!$subject->isClosed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::REOPEN_RECORDS);
    }

    /**
     * Checks whether user can postpone the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isPostponeGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is closed.
        if ($subject->isClosed()) {
            return false;
        }

        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::POSTPONE_RECORDS);
    }

    /**
     * Checks whether user can resume the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isResumeGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is closed.
        if ($subject->isClosed()) {
            return false;
        }

        // Check whether the record is not postponed.
        if (!$subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::RESUME_RECORDS);
    }

    /**
     * Checks whether user can read and post regular comments in the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isPublicCommentsGranted(Record $subject, CurrentUser $user): bool
    {
        return $this->isPermissionGranted($subject, $user, TemplatePermission::ADD_COMMENTS);
    }

    /**
     * Checks whether user can read and post private comments in the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isPrivateCommentsGranted(Record $subject, CurrentUser $user): bool
    {
        return $this->isPermissionGranted($subject, $user, TemplatePermission::PRIVATE_COMMENTS);
    }

    /**
     * Checks whether user can attach files to the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isAttachFileGranted(Record $subject, CurrentUser $user): bool
    {
        return $this->isPermissionGranted($subject, $user, TemplatePermission::ATTACH_FILES);
    }

    /**
     * Checks whether user can delete files of the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isDeleteFileGranted(Record $subject, CurrentUser $user): bool
    {
        return $this->isPermissionGranted($subject, $user, TemplatePermission::DELETE_FILES);
    }

    /**
     * Checks whether user can attach subrecords to the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isAttachSubrecordGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::ATTACH_SUBRECORDS);
    }

    /**
     * Checks whether user can detach subrecords from the specified record.
     *
     * @param   Record      $subject Record.
     * @param   CurrentUser $user    Current user.
     *
     * @return  bool
     */
    protected function isDetachSubrecordGranted(Record $subject, CurrentUser $user): bool
    {
        // Check whether the record is postponed.
        if ($subject->isPostponed()) {
            return false;
        }

        return $this->isPermissionGranted($subject, $user, TemplatePermission::DETACH_SUBRECORDS);
    }
}
