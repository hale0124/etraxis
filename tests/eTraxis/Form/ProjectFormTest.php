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

use eTraxis\Entity\Project;
use eTraxis\Tests\BaseTestCase;

class ProjectFormTest extends BaseTestCase
{
    public function testForm()
    {
        /** @var Project $project */
        $project = $this->doctrine->getRepository(Project::class)->findOneBy(['name' => 'Planet Express']);

        /** @var \Symfony\Component\Form\FormFactoryInterface $factory */
        $factory = $this->client->getContainer()->get('form.factory');

        $form = $factory->create(ProjectForm::class, $project);
        $view = $form->createView();

        $children = $view->children;

        $this->assertEquals($children['name']->vars['data'], $project->getName());
        $this->assertEquals($children['description']->vars['data'], $project->getDescription());
        $this->assertEquals($children['suspended']->vars['data'], $project->isSuspended());
    }
}
