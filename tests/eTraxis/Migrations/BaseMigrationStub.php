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

class BaseMigrationStub extends BaseMigration
{
    public function __construct($driver = 'unsupported')
    {
        $this->connection = new ConnectionStub($driver);
    }

    public function getVersion()
    {
        return '4.0.x';
    }

    public function up(Schema $schema)
    {
        echo 'migrating up';
    }

    public function down(Schema $schema)
    {
        echo 'migrating down';
    }
}
