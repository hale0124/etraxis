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
 * Legacy event types to be converted from 3.9.x to 4.0.0.
 */
class EventType extends StaticDictionary
{
    protected static $dictionary = [
        1  => 'record.created',
        2  => 'record.assigned',
        3  => 'record.edited',
        4  => 'state.changed',
        5  => 'record.postponed',
        6  => 'record.resumed',
        7  => 'comment.public',
        8  => 'file.attached',
        9  => 'file.deleted',
        10 => 'record.cloned',
        11 => 'subrecord.attached',
        12 => 'subrecord.detached',
        13 => 'comment.private',
        14 => 'record.reopened',
    ];
}
