<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form;

use eTraxis\Entity\Group;
use eTraxis\Tests\BaseTestCase;

class GroupFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var Group $group */
        $group = $this->doctrine->getRepository(Group::class)->findOneBy(['name' => 'Staff']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(GroupForm::class, $group);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['name']->vars['data'], $group->getName());
        $this->assertEquals($children['description']->vars['data'], $group->getDescription());
    }
}
