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

class ListValuesRepositoryTest extends BaseTestCase
{
    public function testNewValue()
    {
        $expected = 'Season 8';

        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getManager()->getRepository('eTraxis:Field')->findOneBy(['name' => 'Season']);

        /** @var ListValuesRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository('eTraxis:ListValue');

        $count = count($repository->findAll());

        /** @var \eTraxis\Entity\ListValue $value */
        $value = $repository->findOneBy(['fieldId' => $field->getId(), 'key' => 8]);

        $this->assertNull($value);

        // First attempt.
        $repository->save($field, 8, $expected);

        $value = $repository->findOneBy(['fieldId' => $field->getId(), 'key' => 8]);

        $this->assertNotNull($value);
        $this->assertEquals($expected, $value->getValue());
        $this->assertCount($count + 1, $repository->findAll());

        // Second attempt.
        $repository->save($field, 8, $expected);

        $this->assertCount($count + 1, $repository->findAll());
    }
}
