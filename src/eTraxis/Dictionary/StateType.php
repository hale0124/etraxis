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
    const INITIAL = 'initial';
    const INTERIM = 'interim';
    const FINAL   = 'final';

    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            self::INITIAL => 'state.type.initial',
            self::INTERIM => 'state.type.interim',
            self::FINAL   => 'state.type.final',
        ];
    }
}
