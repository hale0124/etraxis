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
 * Legacy template permissions to be converted from 3.9.x to 4.0.0.
 */
class TemplatePermission extends StaticDictionary
{
    protected static $dictionary = [
        0x0001     => 'record.create',
        0x0002     => 'record.edit',
        0x0004     => 'record.postpone',
        0x0008     => 'record.resume',
        0x0010     => 'record.reassign',
        0x0020     => 'record.reopen',
        0x0040     => 'comment.add',
        0x0080     => 'file.attach',
        0x0100     => 'file.delete',
        0x0200     => 'comment.private',
        0x0400     => 'reminder.send',
        0x0800     => 'record.delete',
        0x1000     => 'subrecord.attach',
        0x2000     => 'subrecord.detach',
        0x40000000 => 'record.view',
    ];
}
