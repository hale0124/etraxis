<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter for "User" objects.
 */
class UserVoter extends AbstractVoter
{
    const DISABLE = 'user.disable';
    const ENABLE  = 'user.enable';
    const UNLOCK  = 'user.unlock';

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
