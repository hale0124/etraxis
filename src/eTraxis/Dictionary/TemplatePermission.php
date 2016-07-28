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
 * Template permissions.
 */
class TemplatePermission extends StaticDictionary
{
    const VIEW_RECORDS      = 'record.view';
    const CREATE_RECORDS    = 'record.create';
    const EDIT_RECORDS      = 'record.edit';
    const POSTPONE_RECORDS  = 'record.postpone';
    const RESUME_RECORDS    = 'record.resume';
    const REASSIGN_RECORDS  = 'record.reassign';
    const REOPEN_RECORDS    = 'record.reopen';
    const ADD_COMMENTS      = 'comment.add';
    const PRIVATE_COMMENTS  = 'comment.private';
    const ATTACH_FILES      = 'file.attach';
    const DELETE_FILES      = 'file.delete';
    const ATTACH_SUBRECORDS = 'subrecord.attach';
    const DETACH_SUBRECORDS = 'subrecord.detach';
    const SEND_REMINDERS    = 'reminder.send';
    const DELETE_RECORDS    = 'record.delete';

    protected static $dictionary = [
        self::VIEW_RECORDS      => 'template.permission.view_records',
        self::CREATE_RECORDS    => 'template.permission.create_records',
        self::EDIT_RECORDS      => 'template.permission.edit_records',
        self::POSTPONE_RECORDS  => 'template.permission.postpone_records',
        self::RESUME_RECORDS    => 'template.permission.resume_records',
        self::REASSIGN_RECORDS  => 'template.permission.reassign_records',
        self::REOPEN_RECORDS    => 'template.permission.reopen_records',
        self::ADD_COMMENTS      => 'template.permission.add_comments',
        self::PRIVATE_COMMENTS  => 'template.permission.private_comments',
        self::ATTACH_FILES      => 'template.permission.attach_files',
        self::DELETE_FILES      => 'template.permission.delete_files',
        self::ATTACH_SUBRECORDS => 'template.permission.attach_subrecords',
        self::DETACH_SUBRECORDS => 'template.permission.detach_subrecords',
        self::SEND_REMINDERS    => 'template.permission.send_reminders',
        self::DELETE_RECORDS    => 'template.permission.delete_records',
    ];
}
