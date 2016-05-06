<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use Doctrine\ORM\EntityRepository;
use eTraxis\Entity\DecimalValue;

/**
 * Decimal values repository.
 */
class DecimalValuesRepository extends EntityRepository implements CustomValuesRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function save(string $value)
    {
        /** @var DecimalValue $entity */
        $entity = $this->findOneBy(['value' => $value]);

        // If entity doesn't exist yet, save it.
        if ($entity === null) {

            $entity = new DecimalValue();
            $entity->setValue($value);

            $em = $this->getEntityManager();

            $em->persist($entity);
            $em->flush($entity);
        }

        return $entity->getId();
    }
}
