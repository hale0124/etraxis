<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\Event;
use eTraxis\Entity\User;
use eTraxis\Repository\EventsRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "User" objects.
 */
class UserVoter extends Voter
{
    protected $repository;
    protected $password_expiration;

    /**
     * Dependency Injection constructor.
     *
     * @param   EventsRepository $repository
     * @param   int              $password_expiration
     */
    public function __construct(EventsRepository $repository, $password_expiration = null)
    {
        $this->repository          = $repository;
        $this->password_expiration = $password_expiration;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attributes = [
            User::SET_EXPIRED_PASSWORD,
            User::DELETE,
            User::DISABLE,
            User::ENABLE,
            User::UNLOCK,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof User;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $subject */
        switch ($attribute) {

            case User::SET_EXPIRED_PASSWORD:
                return $this->isSetExpiredPasswordGranted($subject);

            case User::DELETE:
                return $this->isDeleteGranted($subject, $token->getUser());

            case User::DISABLE:
                return $this->isDisableGranted($subject, $token->getUser());

            case User::ENABLE:
                return $this->isEnableGranted($subject);

            case User::UNLOCK:
                return $this->isUnlockGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether user's password is expired.
     *
     * @param   User $subject Subject user.
     *
     * @return  bool
     */
    protected function isSetExpiredPasswordGranted($subject)
    {
        // Deny if passwords do not expire at all.
        if ($this->password_expiration === null) {
            return false;
        }

        $expires = $subject->getPasswordSetAt() + $this->password_expiration * 86400;

        return $expires < time();
    }

    /**
     * Checks whether specified user can be deleted.
     *
     * @param   User $subject Subject user.
     * @param   User $user    Current user.
     *
     * @return  bool
     */
    protected function isDeleteGranted($subject, $user)
    {
        // User must be logged in.
        if (!$user instanceof User) {
            return false;
        }

        // Can't delete himself.
        if ($subject->getId() == $user->getId()) {
            return false;
        }

        // Number of events originated by subject.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.userId = :id')
            ->setParameter('id', $subject->getId())
        ;

        $countAsOriginator = $query->getQuery()->getSingleScalarResult();

        // Number of issues had been assigned on subject.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.type = :type')
            ->andWhere('e.parameter = :id')
            ->setParameter('type', Event::ISSUE_ASSIGNED)
            ->setParameter('id', $subject->getId())
        ;

        $countAsAssignee = $query->getQuery()->getSingleScalarResult();

        // Can't delete if user is mentioned in any issue log.
        return $countAsOriginator == 0 && $countAsAssignee == 0;
    }

    /**
     * Checks whether specified user can be disabled.
     *
     * @param   User $subject Subject user.
     * @param   User $user    Current user.
     *
     * @return  bool
     */
    protected function isDisableGranted($subject, $user)
    {
        // User must be logged in.
        if (!$user instanceof User) {
            return false;
        }

        // Can't disable himself.
        if ($subject->getId() == $user->getId()) {
            return false;
        }

        return !$subject->isDisabled();
    }

    /**
     * Checks whether specified user can be enabled.
     *
     * @param   User $subject Subject user.
     *
     * @return  bool
     */
    protected function isEnableGranted($subject)
    {
        return $subject->isDisabled();
    }

    /**
     * Checks whether specified user can be unlocked.
     *
     * @param   User $subject Subject user.
     *
     * @return  bool
     */
    protected function isUnlockGranted($subject)
    {
        return !$subject->isAccountNonLocked();
    }
}
