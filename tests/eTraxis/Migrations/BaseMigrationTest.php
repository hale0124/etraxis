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
use eTraxis\Dictionary\DatabasePlatform;

class BaseMigrationTest extends \PHPUnit_Framework_TestCase
{
    public function testVersion()
    {
        $expected  = '4.0.x';
        $migration = new BaseMigrationStub();

        self::assertEquals($expected, $migration->getDescription());
    }

    public function testUpSuccess()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub(DatabasePlatform::MYSQL);

        $this->expectOutputString('migrating up');
        $migration->preUp($schema);
        $migration->up($schema);
    }

    public function testIsMysql()
    {
        $migration = new BaseMigrationStub(DatabasePlatform::MYSQL);

        self::assertTrue($migration->isMysql());
    }

    public function testIsPostgresql()
    {
        $migration = new BaseMigrationStub(DatabasePlatform::POSTGRESQL);

        self::assertTrue($migration->isPostgresql());
    }

    public function testDownSuccess()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub(DatabasePlatform::MYSQL);

        $this->expectOutputString('migrating down');
        $migration->preDown($schema);
        $migration->down($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testUpFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->preUp($schema);
        $migration->up($schema);
    }

    /**
     * @expectedException \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function testDownFailure()
    {
        $schema    = new Schema();
        $migration = new BaseMigrationStub();

        $migration->preDown($schema);
        $migration->down($schema);
    }
}
