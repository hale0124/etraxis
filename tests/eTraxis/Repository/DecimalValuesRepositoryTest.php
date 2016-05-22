<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use eTraxis\Entity\DecimalValue;
use eTraxis\Tests\TransactionalTestCase;

class DecimalValuesRepositoryTest extends TransactionalTestCase
{
    public function testNewValue()
    {
        $expected = '3.14159292';

        /** @var CustomValuesRepositoryInterface $repository */
        $repository = $this->doctrine->getRepository(DecimalValue::class);

        $count = count($repository->findAll());

        /** @var DecimalValue $value */
        $value = $repository->findOneBy(['value' => $expected]);

        self::assertNull($value);

        // First attempt.
        $id1 = $repository->save($expected);

        $value = $repository->findOneBy(['value' => $expected]);

        self::assertEquals($id1, $value->getId());
        self::assertEquals($expected, $value->getValue());
        self::assertCount($count + 1, $repository->findAll());

        // Second attempt.
        $id2 = $repository->save($expected);

        self::assertEquals($id1, $id2);
        self::assertCount($count + 1, $repository->findAll());
    }
}
