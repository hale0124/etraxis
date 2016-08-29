<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Attachments;

use eTraxis\Traits\CommandTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attaches new file.
 *
 * @property    int          $record Record ID.
 * @property    UploadedFile $file   File to attach.
 */
class AttachFileCommand
{
    use CommandTrait;

    /**
     * @Assert\Regex("/^\d+$/")
     */
    public $record;

    /**
     * @Assert\File()
     */
    public $file;
}
