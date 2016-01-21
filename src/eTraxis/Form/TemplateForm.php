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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Template form.
 */
class TemplateForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Template name.
        $builder->add('name', TextType::class, [
            'label'    => 'template.name',
            'attr'     => ['maxlength' => Template::MAX_NAME],
        ]);

        // Template prefix.
        $builder->add('prefix', TextType::class, [
            'label'    => 'template.prefix',
            'attr'     => ['maxlength' => Template::MAX_PREFIX],
        ]);

        // Critical age.
        $builder->add('criticalAge', TextType::class, [
            'label'    => 'template.critical_age',
            'required' => false,
            'attr'     => ['maxlength' => strlen(PHP_INT_MAX)],
        ]);

        // Frozen time.
        $builder->add('frozenTime', TextType::class, [
            'label'    => 'template.frozen_time',
            'required' => false,
            'attr'     => ['maxlength' => strlen(PHP_INT_MAX)],
        ]);

        // Description.
        $builder->add('description', TextType::class, [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => Template::MAX_DESCRIPTION],
        ]);

        // Guest access.
        $builder->add('guestAccess', CheckboxType::class, [
            'label'    => 'template.guest_access',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'template';
    }
}
