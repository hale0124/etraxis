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
 * Event types.
 */
class EventType extends StaticDictionary
{
    const RECORD_CREATED     = 'record.created';
    const RECORD_EDITED      = 'record.edited';
    const RECORD_ASSIGNED    = 'record.assigned';
    const STATE_CHANGED      = 'state.changed';
    const RECORD_POSTPONED   = 'record.postponed';
    const RECORD_RESUMED     = 'record.resumed';
    const RECORD_CLONED      = 'record.cloned';
    const RECORD_REOPENED    = 'record.reopened';
    const PUBLIC_COMMENT     = 'comment.public';
    const PRIVATE_COMMENT    = 'comment.private';
    const FILE_ATTACHED      = 'file.attached';
    const FILE_DELETED       = 'file.deleted';
    const SUBRECORD_ATTACHED = 'subrecord.attached';
    const SUBRECORD_DETACHED = 'subrecord.detached';

    protected static $dictionary = [
        self::RECORD_CREATED     => 'event.record_created',
        self::RECORD_EDITED      => 'event.record_edited',
        self::RECORD_ASSIGNED    => 'event.record_assigned',
        self::STATE_CHANGED      => 'event.state_changed',
        self::RECORD_POSTPONED   => 'event.record_postponed',
        self::RECORD_RESUMED     => 'event.record_resumed',
        self::RECORD_CLONED      => 'event.record_cloned',
        self::RECORD_REOPENED    => 'event.record_reopened',
        self::PUBLIC_COMMENT     => 'event.comment_added',
        self::PRIVATE_COMMENT    => 'event.comment_added',
        self::FILE_ATTACHED      => 'event.file_attached',
        self::FILE_DELETED       => 'event.file_deleted',
        self::SUBRECORD_ATTACHED => 'event.subrecord_attached',
        self::SUBRECORD_DETACHED => 'event.subrecord_detached',
    ];
}
