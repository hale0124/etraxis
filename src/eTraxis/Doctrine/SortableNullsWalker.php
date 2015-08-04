<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Doctrine;

use Doctrine\ORM\Query\SqlWalker;

/**
 * PostgreSQL and Oracle treat NULLs as greatest values.
 * This walker is to workaround it.
 */
class SortableNullsWalker extends SqlWalker
{
    /**
     * {@inheritdoc}
     */
    public function walkOrderByItem($orderByItem)
    {
        $sql = parent::walkOrderByItem($orderByItem);

        if ($orderByItem->isAsc()) {
            $sql .= ' NULLS FIRST';
        }

        if ($orderByItem->isDesc()) {
            $sql .= ' NULLS LAST';
        }

        return $sql;
    }
}
