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
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter for "User" objects.
 */
class UserVoter extends AbstractVoter
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
    protected function getSupportedClasses()
    {
        return ['eTraxis\Entity\User'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [
            User::SET_EXPIRED_PASSWORD,
            User::DELETE,
            User::DISABLE,
            User::ENABLE,
            User::UNLOCK,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var User $object */
        switch ($attribute) {

            case User::SET_EXPIRED_PASSWORD:
                return $this->isSetExpiredPasswordGranted($object);

            case User::DELETE:
                return $this->isDeleteGranted($object, $user);

            case User::DISABLE:
                return $this->isDisableGranted($object, $user);

            case User::ENABLE:
                return $this->isEnableGranted($object);

            case User::UNLOCK:
                return $this->isUnlockGranted($object);

            default:
                return false;
        }
    }

    /**
     * Checks whether user's password is expired.
     *
     * @param   User $object Subject user.
     *
     * @return  bool
     */
    protected function isSetExpiredPasswordGranted($object = null)
    {
        // Deny if passwords do not expire at all.
        if ($this->password_expiration === null) {
            return false;
        }

        $expires = $object->getPasswordSetAt() + $this->password_expiration * 86400;

        return $expires < time();
    }

    /**
     * Checks whether specified user can be deleted.
     *
     * @param   User $object Subject user.
     * @param   User $user   Current user.
     *
     * @return  bool
     */
    protected function isDeleteGranted($object, $user = null)
    {
        /** @var User $user */
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Can't delete himself.
        if ($object->getId() == $user->getId()) {
            return false;
        }

        // Number of events originated by subject.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.userId = :id')
            ->setParameter('id', $object->getId())
        ;

        $countAsOriginator = $query->getQuery()->getSingleScalarResult();

        // Number of issues had been assigned on subject.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.type = :type')
            ->andWhere('e.parameter = :id')
            ->setParameter('type', Event::ISSUE_ASSIGNED)
            ->setParameter('id', $object->getId())
        ;

        $countAsAssignee = $query->getQuery()->getSingleScalarResult();

        // Can't delete if user is mentioned in any issue log.
        return $countAsOriginator == 0 && $countAsAssignee == 0;
    }

    /**
     * Checks whether specified user can be disabled.
     *
     * @param   User $object Subject user.
     * @param   User $user   Current user.
     *
     * @return  bool
     */
    protected function isDisableGranted($object, $user = null)
    {
        /** @var User $user */
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Can't disable himself.
        if ($object->getId() == $user->getId()) {
            return false;
        }

        return !$object->isDisabled();
    }

    /**
     * Checks whether specified user can be enabled.
     *
     * @param   User $object Subject user.
     *
     * @return  bool
     */
    protected function isEnableGranted($object)
    {
        return $object->isDisabled();
    }

    /**
     * Checks whether specified user can be unlocked.
     *
     * @param   User $object Subject user.
     *
     * @return  bool
     */
    protected function isUnlockGranted($object)
    {
        return !$object->isAccountNonLocked();
    }
}
