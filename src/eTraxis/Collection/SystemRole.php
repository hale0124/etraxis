<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Collection;

/**
 * System role.
 */
class SystemRole
{
    const AUTHOR      = -1;  // creator of the issue
    const RESPONSIBLE = -2;  // user assigned on the issue
    const REGISTERED  = -3;  // any authenticated user
}
