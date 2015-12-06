<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Repository;

use eTraxis\Tests\BaseTestCase;

class StringValuesRepositoryTest extends BaseTestCase
{
    public function testNewValue()
    {
        $expected = 'Artem Rodygin';

        /** @var StringValuesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:StringValue');

        $count = count($repository->findAll());

        /** @var \eTraxis\Entity\StringValue $value */
        $value = $repository->findOneBy(['value' => $expected]);

        $this->assertNull($value);

        // First attempt.
        $id1 = $repository->save($expected);

        $value = $repository->findOneBy(['value' => $expected]);

        $this->assertNotNull($value);
        $this->assertEquals($id1, $value->getId());
        $this->assertEquals($expected, $value->getValue());
        $this->assertCount($count + 1, $repository->findAll());

        // Second attempt.
        $id2 = $repository->save($expected);

        $this->assertEquals($id1, $id2);
        $this->assertCount($count + 1, $repository->findAll());
    }
}
