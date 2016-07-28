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
 * State types.
 */
class StateType extends StaticDictionary
{
    const IS_INITIAL = 'initial';
    const IS_INTERIM = 'interim';
    const IS_FINAL   = 'final';

    protected static $dictionary = [
        self::IS_INITIAL => 'state.type.initial',
        self::IS_INTERIM => 'state.type.interim',
        self::IS_FINAL   => 'state.type.final',
    ];
}
