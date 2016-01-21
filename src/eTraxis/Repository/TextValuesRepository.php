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
use eTraxis\Entity\TextValue;

/**
 * Text values repository.
 */
class TextValuesRepository extends EntityRepository
{
    /**
     * Saves specified value in the repository and returns its ID.
     *
     * @param   string $value Text value.
     *
     * @return  int Value ID.
     */
    public function save($value)
    {
        $token = md5($value);

        /** @var \eTraxis\Entity\TextValue $entity */
        $entity = $this->findOneBy(['token' => $token]);

        // If entity doesn't exist yet, save it.
        if ($entity === null) {

            $entity = new TextValue();

            $entity
                ->setToken($token)
                ->setValue($value)
            ;

            $em = $this->getEntityManager();

            $em->persist($entity);
            $em->flush($entity);
        }

        return $entity->getId();
    }
}
