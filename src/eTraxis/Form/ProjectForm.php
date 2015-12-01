<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form;

use eTraxis\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Project form.
 */
class ProjectForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Project name.
        $builder->add('name', 'text', [
            'label'    => 'project.name',
            'attr'     => ['maxlength' => Project::MAX_NAME],
        ]);

        // Description.
        $builder->add('description', 'text', [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => Project::MAX_DESCRIPTION],
        ]);

        // Suspended.
        $builder->add('suspended', 'checkbox', [
            'label'    => 'project.suspended',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'project';
    }
}
