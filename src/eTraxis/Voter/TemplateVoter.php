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
use eTraxis\Entity\Record;
use eTraxis\Entity\State;
use eTraxis\Entity\Template;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Template" objects.
 */
class TemplateVoter extends Voter
{
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
            Template::DELETE,
            Template::LOCK,
            Template::UNLOCK,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof Template;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Template $subject */
        switch ($attribute) {

            case Template::DELETE:
                return $this->isDeleteGranted($subject);

            case Template::LOCK:
                return $this->isLockGranted($subject);

            case Template::UNLOCK:
                return $this->isUnlockGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified template can be deleted.
     *
     * @param   Template $subject Template.
     *
     * @return  bool
     */
    protected function isDeleteGranted(Template $subject)
    {
        // Number of records created by the template.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(Record::class, 'r')
            ->leftJoin('r.state', 's')
            ->where('s.template = :template')
            ->setParameter('template', $subject)
        ;

        $count = (int) $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one record has been created by this template.
        return $count === 0;
    }

    /**
     * Checks whether specified template can be locked.
     *
     * @param   Template $subject Template.
     *
     * @return  bool
     */
    protected function isLockGranted(Template $subject)
    {
        return !$subject->isLocked();
    }

    /**
     * Checks whether specified template can be unlocked.
     *
     * @param   Template $subject Template.
     *
     * @return  bool
     */
    protected function isUnlockGranted(Template $subject)
    {
        if (!$subject->isLocked()) {
            return false;
        }

        // Number of initial states of the template.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from(State::class, 's')
            ->where('s.template = :template')
            ->andWhere('s.type = :type')
            ->setParameter('template', $subject)
            ->setParameter('type', State::TYPE_INITIAL)
        ;

        $count = (int) $query->getQuery()->getSingleScalarResult();

        // Can't unlock if no initial state is set in the template.
        return $count !== 0;
    }
}
