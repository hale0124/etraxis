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
use eTraxis\Dictionary\SystemRole;

class StateRoleTransitionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $template = new Template(new Project());

        $from = new State($template, StateType::IS_INTERIM);
        $to   = new State($template, StateType::IS_INTERIM);
        $role = SystemRole::AUTHOR;

        $from->setName('From');
        $to->setName('To');

        $object = new StateRoleTransition($from, $to, $role);

        self::assertEquals($from, $object->getFromState());
        self::assertEquals($to, $object->getToState());
        self::assertEquals($role, $object->getRole());
    }
}
