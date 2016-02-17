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

use eTraxis\Entity\State;
use eTraxis\Tests\BaseTestCase;

class StateFormTest extends BaseTestCase
{
    public function testFormNew()
    {
        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(StateForm::class);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEmpty($children['name']->vars['data']);
        $this->assertEmpty($children['abbreviation']->vars['data']);
        $this->assertEquals($children['type']->vars['data'], State::TYPE_INTERIM);
        $this->assertEmpty($children['responsible']->vars['data']);
    }

    public function testFormInitialState()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'New']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(StateForm::class, $state);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['name']->vars['data'], $state->getName());
        $this->assertEquals($children['abbreviation']->vars['data'], $state->getAbbreviation());
        $this->assertEquals($children['type']->vars['data'], $state->getType());
        $this->assertEquals($children['responsible']->vars['data'], $state->getResponsible());
    }

    public function testFormFinalState()
    {
        /** @var State $state */
        $state = $this->doctrine->getRepository(State::class)->findOneBy(['name' => 'Delivered']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(StateForm::class, $state);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['name']->vars['data'], $state->getName());
        $this->assertEquals($children['abbreviation']->vars['data'], $state->getAbbreviation());
        $this->assertEquals($children['type']->vars['data'], $state->getType());
        $this->assertEquals($children['responsible']->vars['data'], $state->getResponsible());
    }
}
