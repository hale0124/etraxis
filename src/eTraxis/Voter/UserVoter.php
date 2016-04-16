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
use eTraxis\Entity\Event;
use eTraxis\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "User" objects.
 */
class UserVoter extends Voter
{
    protected $manager;
    protected $password_expiration;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     * @param   int                    $password_expiration
     */
    public function __construct(EntityManagerInterface $manager, $password_expiration = null)
    {
        $this->manager             = $manager;
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

        return $subject->isPasswordExpired($this->password_expiration);
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
        if ($subject === $user) {
            return false;
        }

        // Number of events originated by subject.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Event::class, 'e')
            ->where('e.user = :user')
            ->setParameter('user', $subject)
        ;

        $countAsOriginator = (int) $query->getQuery()->getSingleScalarResult();

        // Number of records had been assigned on subject.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Event::class, 'e')
            ->where('e.type = :type')
            ->andWhere('e.parameter = :id')
            ->setParameter('type', Event::RECORD_ASSIGNED)
            ->setParameter('id', $subject->getId())
        ;

        $countAsAssignee = (int) $query->getQuery()->getSingleScalarResult();

        // Can't delete if user is mentioned in any record log.
        return $countAsOriginator === 0 && $countAsAssignee === 0;
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
        if ($subject === $user) {
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
