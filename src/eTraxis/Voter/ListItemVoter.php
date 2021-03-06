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
use eTraxis\Entity\FieldValue;
use eTraxis\Entity\ListItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "ListItem" objects.
 */
class ListItemVoter extends Voter
{
    const DELETE = 'listitem.delete';

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
            self::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
            return $subject instanceof ListItem;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var ListItem $subject */
        switch ($attribute) {

            case self::DELETE:
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
    protected function isDeleteGranted(ListItem $subject): bool
    {
        // Number of records where the list item is used.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(v.event)')
            ->from(FieldValue::class, 'v')
            ->where('v.field = :field')
            ->andWhere('v.value = :value')
            ->setParameter('field', $subject->getField())
            ->setParameter('value', $subject->getValue())
        ;

        $count = (int) $query->getQuery()->getSingleScalarResult();

        // Can't delete if this value has been appeared in at least one record.
        return $count === 0;
    }
}
