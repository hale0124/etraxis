<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use Doctrine\ORM\EntityRepository;
use eTraxis\Entity\ListValue;

/**
 * List values repository.
 */
class ListValuesRepository extends EntityRepository
{
    /**
     * Saves specified value in the repository.
     *
     * @param   \eTraxis\Entity\Field  $field Field.
     * @param   int                    $key   Value key.
     * @param   string                 $value String value.
     */
    public function save($field, $key, $value)
    {
        /** @var \eTraxis\Entity\ListValue $entity */
        $entity = $this->findOneBy([
            'fieldId' => $field->getId(),
            'key'     => $key,
        ]);

        // If entity doesn't exist yet, save it.
        if ($entity === null) {

            $entity = new ListValue();

            $entity
                ->setFieldId($field->getId())
                ->setField($field)
                ->setKey($key)
            ;
        }

        $entity->setValue($value);

        $em = $this->getEntityManager();

        $em->persist($entity);
        $em->flush($entity);
    }
}
