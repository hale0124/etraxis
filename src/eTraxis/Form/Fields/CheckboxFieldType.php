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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Additional inputs for "checkbox" field form.
 */
class CheckboxFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Default value.
        $builder->add('default', CheckboxType::class, [
            'label'    => 'field.default_value',
            'required' => false,
            'attr'     => [
                'data-id' => 'default',
                'class'   => 'field-type-specific',
            ],
        ]);
    }
}
