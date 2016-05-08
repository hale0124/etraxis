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
use eTraxis\Entity\Template;

/**
 * Template permissions.
 */
class TemplatePermission extends StaticDictionary
{
    /**
     * {@inheritdoc}
     */
    public static function all()
    {
        return [
            Template::PERMIT_VIEW_RECORD      => 'template.permission.view_records',
            Template::PERMIT_CREATE_RECORD    => 'template.permission.create_records',
            Template::PERMIT_EDIT_RECORD      => 'template.permission.edit_records',
            Template::PERMIT_POSTPONE_RECORD  => 'template.permission.postpone_records',
            Template::PERMIT_RESUME_RECORD    => 'template.permission.resume_records',
            Template::PERMIT_REASSIGN_RECORD  => 'template.permission.reassign_records',
            Template::PERMIT_REOPEN_RECORD    => 'template.permission.reopen_records',
            Template::PERMIT_ADD_COMMENT      => 'template.permission.add_comments',
            Template::PERMIT_ADD_FILE         => 'template.permission.add_files',
            Template::PERMIT_REMOVE_FILE      => 'template.permission.remove_files',
            Template::PERMIT_PRIVATE_COMMENT  => 'template.permission.private_comments',
            Template::PERMIT_SEND_REMINDER    => 'template.permission.send_reminders',
            Template::PERMIT_DELETE_RECORD    => 'template.permission.delete_records',
            Template::PERMIT_ATTACH_SUBRECORD => 'template.permission.attach_subrecords',
            Template::PERMIT_DETACH_SUBRECORD => 'template.permission.detach_subrecords',
        ];
    }
}
