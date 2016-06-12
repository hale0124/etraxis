<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use eTraxis\Dictionary\StateType;

class StateResponsibleGroupTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $template = new Template(new Project());

        $state = new State($template, StateType::IS_INTERIM);
        $group = new Group();

        $object = new StateResponsibleGroup($state, $group);

        self::assertEquals($state, $object->getState());
        self::assertEquals($group, $object->getGroup());
    }
}
