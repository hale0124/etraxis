<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use eTraxis\Entity\Field;

/**
 * The filter to skip fields marked as deleted.
 */
class FieldsFilter extends SQLFilter
{
    /**
     * {@inheritdoc}
     */
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getName() != Field::class) {
            return '';
        }

        return $targetTableAlias . '.removal_time = 0';
    }
}
