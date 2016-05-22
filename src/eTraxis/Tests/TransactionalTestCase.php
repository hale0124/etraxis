<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Tests;

/**
 * Extended base test case with every test wrapped into database transaction.
 */
class TransactionalTestCase extends ControllerTestCase
{
    /**
     * Maximum value of signed 32-bits integer which can be used as an ID of non-existing entity.
     * The "PHP_INT_MAX" cannot be used as it causes "value 9223372036854775807 is out of range for type integer"
     * SQL driver error for PostgreSQL on 64-bits platforms.
     */
    const UNKNOWN_ENTITY_ID = 0x7FFFFFFF;

    /** @var \Symfony\Bridge\Doctrine\RegistryInterface */
    protected $doctrine;

    /**
     * Begins new transaction.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->doctrine = $this->client->getContainer()->get('doctrine');

        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();
        $manager->beginTransaction();
    }

    /**
     * Rolls back current transaction.
     */
    protected function tearDown()
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $manager */
        $manager = $this->doctrine->getManager();
        $manager->rollback();

        parent::tearDown();
    }
}
