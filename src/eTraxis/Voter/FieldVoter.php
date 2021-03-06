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

use eTraxis\Entity\Field;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Field" objects.
 */
class FieldVoter extends Voter
{
    const DELETE = 'field.delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attributes = [
            self::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof Field;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Field $subject */
        switch ($attribute) {

            case self::DELETE:
                return $this->isDeleteGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified field can be deleted.
     *
     * @param   Field $subject Field.
     *
     * @return  bool
     */
    protected function isDeleteGranted(Field $subject): bool
    {
        // Can't delete if owning template is not locked.
        return $subject->getState()->getTemplate()->isLocked();
    }
}
