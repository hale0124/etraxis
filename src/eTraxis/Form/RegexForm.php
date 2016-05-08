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

use eTraxis\Entity\FieldRegex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PCRE form.
 */
class RegexForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // PCRE to check field values.
        $builder->add('check', TextType::class, [
            'label'    => 'field.regex_check',
            'required' => false,
            'attr'     => ['maxlength' => FieldRegex::MAX_REGEX],
        ]);

        // Search PCRE to transform field values.
        $builder->add('search', TextType::class, [
            'label'    => 'field.regex_search',
            'required' => false,
            'attr'     => ['maxlength' => FieldRegex::MAX_REGEX],
        ]);

        // Replace PCRE to transform field values.
        $builder->add('replace', TextType::class, [
            'label'    => 'field.regex_replace',
            'required' => false,
            'attr'     => ['maxlength' => FieldRegex::MAX_REGEX],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'regex';
    }
}
