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
    const VIEW            = 'record.view';
    const PUBLIC_COMMENT  = 'record.public_comment';
    const PRIVATE_COMMENT = 'record.private_comment';

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
            self::PUBLIC_COMMENT,
            self::PRIVATE_COMMENT,
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

            case self::PUBLIC_COMMENT:
                return $this->isPublicCommentsGranted($subject, $token->getUser());

            case self::PRIVATE_COMMENT:
                return $this->isPrivateCommentsGranted($subject, $token->getUser());

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
        if ($subject->getResponsible() && $subject->getResponsible()->getId() === $user->getId()) {
            if ($subject->getTemplate()->isRoleGranted(SystemRole::RESPONSIBLE, $permission)) {
                return true;
            }
        }

        // Check whether current user belongs to any group which is granted specified permission.
        return $subject->getTemplate()->isUserGranted($user, $permission);
    }

    /**
     * Checks whether specified record can be viewed.
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
        if ($subject->getResponsible() && $subject->getResponsible()->getId() === $user->getId()) {
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
}
