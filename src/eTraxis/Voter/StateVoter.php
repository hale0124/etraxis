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
use eTraxis\Entity\State;
use eTraxis\Repository\EventsRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "State" objects.
 */
class StateVoter extends Voter
{
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   EventsRepository $repository
     */
    public function __construct(EventsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attributes = [
            State::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof State;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var State $subject */
        switch ($attribute) {

            case State::DELETE:
                return $this->isDeleteGranted($subject);

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
    protected function isDeleteGranted($subject)
    {
        // Number of issues appeared in the state.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.parameter = :id')
            ->andWhere('e.type IN (:types)')
            ->setParameter('id', $subject->getId())
            ->setParameter('types', [Event::ISSUE_CREATED, Event::ISSUE_REOPENED, Event::STATE_CHANGED])
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one issue has been appeared in this state.
        return $count == 0;
    }
}
