<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Dictionary\Legacy;

use Dictionary\StaticDictionary;

/**
 * Legacy state types to be converted from 3.9.x to 4.0.0.
 */
class StateType extends StaticDictionary
{
    protected static $dictionary = [
        1 => 'initial',
        2 => 'interim',
        3 => 'final',
    ];
}
