<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity\Fields;

use Doctrine\ORM\Event\LifecycleEventArgs;
use eTraxis\Entity\Field;
use eTraxis\Tests\BaseTestCase;

class FieldListenerTest extends BaseTestCase
{
    /** @var FieldListener */
    private $object;

    protected function setUp()
    {
        parent::setUp();

        $this->object = new FieldListener();
    }

    public function testPostLoad()
    {
        $field = new Field();

        $event = new LifecycleEventArgs($field, $this->doctrine->getManager());

        $this->object->postLoad($field, $event);
    }
}
