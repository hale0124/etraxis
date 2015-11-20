<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields;

use eTraxis\Tests\BaseTestCase;

class DeleteFieldCommandTest extends BaseTestCase
{
    public function testSuccess()
    {
        /** @var \eTraxis\Entity\Field $field */
        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Crew',
            'removedAt' => 0,
        ]);

        $this->assertNotNull($field);

        $command = new DeleteFieldCommand(['id' => $field->getId()]);
        $this->command_bus->handle($command);

        $field = $this->doctrine->getRepository('eTraxis:Field')->findOneBy([
            'name'      => 'Crew',
            'removedAt' => 0,
        ]);

        $this->assertNull($field);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testNotFound()
    {
        $command = new DeleteFieldCommand(['id' => $this->getMaxId()]);
        $this->command_bus->handle($command);
    }
}
