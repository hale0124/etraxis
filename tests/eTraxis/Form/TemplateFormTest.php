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

use eTraxis\Entity\Template;
use eTraxis\Tests\BaseTestCase;

class TemplateFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var Template $template */
        $template = $this->doctrine->getRepository(Template::class)->findOneBy(['name' => 'Delivery']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(TemplateForm::class, $template);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['name']->vars['data'], $template->getName());
        $this->assertEquals($children['prefix']->vars['data'], $template->getPrefix());
        $this->assertEquals($children['criticalAge']->vars['data'], $template->getCriticalAge());
        $this->assertEquals($children['frozenTime']->vars['data'], $template->getFrozenTime());
        $this->assertEquals($children['description']->vars['data'], $template->getDescription());
        $this->assertEquals($children['guestAccess']->vars['data'], $template->hasGuestAccess());
    }
}
