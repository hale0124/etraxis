<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Additional inputs for "duration" field form.
 */
class DurationFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Min value.
        $builder->add('minValue', TextType::class, [
            'label'    => 'field.min_value',
            'required' => true,
            'attr'     => [
                'maxlength' => strlen('000000:00'),
                'data-id'   => 'minValue',
                'class'     => 'field-type-specific',
            ],
        ]);

        // Max value.
        $builder->add('maxValue', TextType::class, [
            'label'    => 'field.max_value',
            'required' => true,
            'attr'     => [
                'maxlength' => strlen('000000:00'),
                'data-id'   => 'maxValue',
                'class'     => 'field-type-specific',
            ],
        ]);

        // Default value.
        $builder->add('defaultValue', TextType::class, [
            'label'    => 'field.default_value',
            'required' => false,
            'attr'     => [
                'maxlength' => strlen('000000:00'),
                'data-id'   => 'default',
                'class'     => 'field-type-specific',
            ],
        ]);
    }
}
