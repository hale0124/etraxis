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
use eTraxis\Dictionary\EventType;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\Event;
use eTraxis\Entity\State;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "State" objects.
 */
class StateVoter extends Voter
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
            State::DELETE,
            State::INITIAL,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof State;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var State $subject */
        switch ($attribute) {

            case State::DELETE:
                return $this->isDeleteGranted($subject);

            case State::INITIAL:
                return $this->isInitialGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified state can be deleted.
     *
     * @param   State $subject State.
     *
     * @return  bool
     */
    protected function isDeleteGranted(State $subject): bool
    {
        // Number of records appeared in the state.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from(Event::class, 'e')
            ->where('e.parameter = :id')
            ->andWhere('e.type IN (:types)')
            ->setParameter('id', $subject->getId())
            ->setParameter('types', [EventType::RECORD_CREATED, EventType::RECORD_REOPENED, EventType::STATE_CHANGED])
        ;

        $count = (int) $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one record has been appeared in this state.
        return $count === 0;
    }

    /**
     * Checks whether specified state can be set as initial.
     *
     * @param   State $subject State.
     *
     * @return  bool
     */
    protected function isInitialGranted(State $subject): bool
    {
        return $subject->getType() === StateType::INTERIM;
    }
}
