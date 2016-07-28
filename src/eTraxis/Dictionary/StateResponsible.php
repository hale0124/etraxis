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

/**
 * State responsibility values.
 */
class StateResponsible extends StaticDictionary
{
    const KEEP   = 'keep';
    const ASSIGN = 'assign';
    const REMOVE = 'remove';

    protected static $dictionary = [
        self::KEEP   => 'state.responsible.keep',
        self::ASSIGN => 'state.responsible.assign',
        self::REMOVE => 'state.responsible.remove',
    ];
}
