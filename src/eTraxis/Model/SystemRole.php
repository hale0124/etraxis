<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Model;

/**
 * System role.
 */
class SystemRole
{
    const AUTHOR      = -1;  // creator of the issue
    const RESPONSIBLE = -2;  // user assigned on the issue
    const REGISTERED  = -3;  // any authenticated user
}
