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
    const VIEW = 'record.view';

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

            default:
                return false;
        }
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
}
