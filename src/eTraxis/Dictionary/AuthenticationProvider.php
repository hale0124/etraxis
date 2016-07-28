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
 * Authentication providers.
 */
class AuthenticationProvider extends StaticDictionary
{
    const FALLBACK = self::ETRAXIS;

    const ETRAXIS = 'etraxis';
    const LDAP    = 'ldap';

    protected static $dictionary = [
        self::ETRAXIS => 'eTraxis',
        self::LDAP    => 'LDAP',
    ];
}
