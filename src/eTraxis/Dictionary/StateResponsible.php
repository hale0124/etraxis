<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary;

use Dictionary\StaticDictionary;
use eTraxis\Entity\State;

/**
 * State responsibility values.
 */
class StateResponsible extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            State::RESPONSIBLE_KEEP   => 'state.responsible.keep',
            State::RESPONSIBLE_ASSIGN => 'state.responsible.assign',
            State::RESPONSIBLE_REMOVE => 'state.responsible.remove',
        ];
    }
}
