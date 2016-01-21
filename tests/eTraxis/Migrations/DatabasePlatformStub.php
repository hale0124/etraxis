<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Migrations;

class DatabasePlatformStub
{
    private $driver = null;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function getName()
    {
        return $this->driver;
    }
}
