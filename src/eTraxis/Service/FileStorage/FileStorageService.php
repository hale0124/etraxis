<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Service\FileStorage;

use eTraxis\Service\FileStorageInterface;

/**
 * File storage service.
 */
class FileStorageService implements FileStorageInterface
{
    protected $files_path;

    /**
     * Dependency Injection constructor.
     *
     * @param   string $files_path
     */
    public function __construct(string $files_path)
    {
        $this->files_path = realpath($files_path);
    }

    /**
     * {@inheritdoc}
     */
    public function getAbsolutePath(string $filename): string
    {
        return realpath($this->files_path . '/' . $filename);
    }
}
