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

use eTraxis\Entity\ListItem;
use eTraxis\Repository\FieldValuesRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "ListItem" objects.
 */
class ListItemVoter extends Voter
{
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   FieldValuesRepository $repository
     */
    public function __construct(FieldValuesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        $attributes = [
            ListItem::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof ListItem;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var ListItem $subject */
        switch ($attribute) {

            case ListItem::DELETE:
                return $this->isDeleteGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified list item can be deleted.
     *
     * @param   ListItem $subject List item.
     *
     * @return  bool
     */
    protected function isDeleteGranted($subject)
    {
        // Number of issues where the list item is used.
        $query = $this->repository->createQueryBuilder('v')
            ->select('COUNT(v.eventId)')
            ->where('v.fieldId = :field')
            ->andWhere('v.valueId = :value')
            ->setParameter('field', $subject->getFieldId())
            ->setParameter('value', $subject->getKey())
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if this value has been appeared in at least one issue.
        return $count == 0;
    }
}
