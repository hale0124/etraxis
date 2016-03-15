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

use Doctrine\DBAL\Schema\Schema;

class OracleMigrationStub extends BaseMigrationStub implements OracleMigrationInterface
{
    public function __construct()
    {
        parent::__construct('oracle');
    }

    public function oracleUp(Schema $schema)
    {
        echo 'oracle up';
    }

    public function oracleDown(Schema $schema)
    {
        echo 'oracle down';
    }
}
