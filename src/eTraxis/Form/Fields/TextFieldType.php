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

use eTraxis\Entity\Fields\TextField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Additional inputs for "text" field form.
 */
class TextFieldType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Max length.
        $builder->add('maxLength', TextType::class, [
            'label'    => 'field.max_length',
            'required' => true,
            'attr'     => [
                'maxlength' => strlen(TextField::MAX_LENGTH),
                'data-id'   => 'maxLength',
                'class'     => 'field-type-specific',
            ],
        ]);

        // Default value.
        $builder->add('default', TextareaType::class, [
            'label'    => 'field.default_value',
            'required' => false,
            'attr'     => [
                'maxlength' => TextField::MAX_LENGTH,
                'data-id'   => 'default',
                'class'     => 'field-type-specific',
            ],
        ]);
    }
}
