<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

use eTraxis\Entity\State;

/**
 * Static collection of state responsibility values.
 */
class StateResponsible extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            State::RESPONSIBLE_KEEP   => 'responsible.keep',
            State::RESPONSIBLE_ASSIGN => 'responsible.assign',
            State::RESPONSIBLE_REMOVE => 'responsible.remove',
        ];
    }
}
