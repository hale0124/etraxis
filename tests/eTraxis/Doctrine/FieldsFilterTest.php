<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Doctrine;

use eTraxis\Entity\Field;
use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class FieldsFilterTest extends BaseTestCase
{
    public function testFields()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Produced']);
        $this->assertNotNull($state);

        $this->assertCount(7, $state->getFields());

        /** @var Field $field */
        $field = $state->getFields()->get(6);

        $this->doctrine->getManager()->remove($field);
        $this->doctrine->getManager()->flush();

        $state = $this->doctrine->getRepository(State::class)->find($state->getId());

        $this->assertCount(6, $state->getFields());
    }
}
