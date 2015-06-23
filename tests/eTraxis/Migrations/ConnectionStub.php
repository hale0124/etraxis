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

namespace eTraxis\Migrations;

class ConnectionStub
{
    private $driver = null;

    public function __construct($driver)
    {
        $this->driver = $driver;
    }

    public function getDatabasePlatform()
    {
        return new DatabasePlatformStub($this->driver);
    }
}
