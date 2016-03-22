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

use eTraxis\Collection\FieldType;
use eTraxis\Entity\Field;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Field form.
 */
class FieldForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Field name.
        $builder->add('name', TextType::class, [
            'label'    => 'field.name',
            'required' => true,
            'attr'     => ['maxlength' => Field::MAX_NAME],
        ]);

        // Field type.
        $builder->add('type', ChoiceType::class, [
            'label'    => 'field.type',
            'required' => true,
            'choices'  => array_flip(FieldType::getCollection()),
            'data'     => Field::TYPE_STRING,
        ]);

        // Type-specific inputs.
        $builder->add('asNumber',   Fields\NumberFieldType::class);
        $builder->add('asDecimal',  Fields\DecimalFieldType::class);
        $builder->add('asString',   Fields\StringFieldType::class);
        $builder->add('asText',     Fields\TextFieldType::class);
        $builder->add('asCheckbox', Fields\CheckboxFieldType::class);
        // no specific inputs for "list"
        // no specific inputs for "record"
        $builder->add('asDate',     Fields\DateFieldType::class);
        $builder->add('asDuration', Fields\DurationFieldType::class);

        // Description.
        $builder->add('description', TextType::class, [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => Field::MAX_DESCRIPTION],
        ]);

        // Required.
        $builder->add('required', CheckboxType::class, [
            'label'    => 'field.required',
            'required' => false,
        ]);

        // Guest access.
        $builder->add('guestAccess', CheckboxType::class, [
            'label'    => 'field.guest_access',
            'required' => false,
        ]);

        // Show in emails.
        $builder->add('showInEmails', CheckboxType::class, [
            'label'    => 'field.show_in_emails',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'field';
    }
}
