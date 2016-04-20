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
 * Static collection of state types.
 */
class StateType extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            State::TYPE_INITIAL => 'state.type.initial',
            State::TYPE_INTERIM => 'state.type.interim',
            State::TYPE_FINAL   => 'state.type.final',
        ];
    }
}
