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
 * Static collection of state types.
 */
class StateType extends AbstractStaticCollection
{
    /**
     * {@inheritdoc}
     */
    public static function getCollection()
    {
        return [
            State::TYPE_INITIAL   => 'type.initial',
            State::TYPE_TRANSIENT => 'type.transient',
            State::TYPE_FINAL     => 'type.final',
        ];
    }
}
