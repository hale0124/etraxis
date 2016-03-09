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

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class FieldsRepositoryTest extends BaseTestCase
{
    public function testGetFields()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);
        $this->assertNotNull($state);

        /** @var FieldsRepository $repository */
        $repository = $this->doctrine->getManager()->getRepository(Field::class);

        $result = $repository->getFields($state->getId());

        $fields = array_map(function ($field) {
            return $field['name'];
        }, $result);

        $expected = [
            'Crew',
            'Delivery to',
            'Delivery at',
            'Notes',
        ];

        $this->assertEquals($expected, $fields);
    }
}
