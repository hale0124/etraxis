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
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Voter for "State" objects.
 */
class StateVoter extends AbstractVoter
{
    const DELETE = 'state.delete';

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
    protected function getSupportedClasses()
    {
        return ['eTraxis\Entity\State'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [
            self::DELETE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var State $object */
        switch ($attribute) {

            case self::DELETE:
                return $this->isDeleteGranted($object);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified state can be deleted.
     *
     * @param   State $object State.
     *
     * @return  bool
     */
    protected function isDeleteGranted($object)
    {
        // Number of issues appeared in the state.
        $query = $this->repository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.parameter = :id')
            ->andWhere('e.type IN (:types)')
            ->setParameter('id', $object->getId())
            ->setParameter('types', [Event::ISSUE_CREATED, Event::ISSUE_REOPENED, Event::STATE_CHANGED])
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one issue has been appeared in this state.
        return $count == 0;
    }
}
