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
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter for "User" objects.
 */
class UserVoter extends AbstractVoter
{
    const SET_EXPIRED_PASSWORD = 'user.set_expired_password';
    const DELETE               = 'user.delete';
    const DISABLE              = 'user.disable';
    const ENABLE               = 'user.enable';
    const UNLOCK               = 'user.unlock';

    protected $doctrine;
    protected $password_expiration;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     * @param   int               $password_expiration
     */
    public function __construct(RegistryInterface $doctrine, $password_expiration = null)
    {
        $this->doctrine            = $doctrine;
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
            self::SET_EXPIRED_PASSWORD,
            self::DELETE,
            self::DISABLE,
            self::ENABLE,
            self::UNLOCK,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var User $object */
        switch ($attribute) {

            case self::SET_EXPIRED_PASSWORD:
                return $this->isSetExpiredPasswordGranted($object);

            case self::DELETE:
                return $this->isDeleteGranted($object, $user);

            case self::DISABLE:
                return $this->isDisableGranted($object, $user);

            case self::ENABLE:
                return $this->isEnableGranted($object, $user);

            case self::UNLOCK:
                return $this->isUnlockGranted($object, $user);

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
     * Checks whether current user can delete specified user.
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

        if (!$user->isAdmin()) {
            return false;
        }

        // Can't delete himself.
        if ($object->getId() == $user->getId()) {
            return false;
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Event');

        // Number of events originated by subject.
        $query = $repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.userId = :id')
            ->setParameter('id', $object->getId())
        ;

        $countAsOriginator = $query->getQuery()->getSingleScalarResult();

        // Number of issues had been assigned on subject.
        $query = $repository->createQueryBuilder('e')
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
     * Checks whether current user can disable specified user.
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

        if (!$user->isAdmin()) {
            return false;
        }

        // Can't disable himself.
        if ($object->getId() == $user->getId()) {
            return false;
        }

        return !$object->isDisabled();
    }

    /**
     * Checks whether current user can enable specified user.
     *
     * @param   User $object Subject user.
     * @param   User $user   Current user.
     *
     * @return  bool
     */
    protected function isEnableGranted($object, $user = null)
    {
        /** @var User $user */
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        return $object->isDisabled();
    }

    /**
     * Checks whether current user can unlock specified user.
     *
     * @param   User $object Subject user.
     * @param   User $user   Current user.
     *
     * @return  bool
     */
    protected function isUnlockGranted($object, $user = null)
    {
        /** @var User $user */
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        return !$object->isAccountNonLocked();
    }
}
