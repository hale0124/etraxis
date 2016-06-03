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

use eTraxis\Entity\FieldPCRE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * PCRE form.
 */
class PCREForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // PCRE to check field values.
        $builder->add('check', TextType::class, [
            'label'    => 'field.pcre_check',
            'required' => false,
            'attr'     => ['maxlength' => FieldPCRE::MAX_PCRE],
        ]);

        // Search PCRE to transform field values.
        $builder->add('search', TextType::class, [
            'label'    => 'field.pcre_search',
            'required' => false,
            'attr'     => ['maxlength' => FieldPCRE::MAX_PCRE],
        ]);

        // Replace PCRE to transform field values.
        $builder->add('replace', TextType::class, [
            'label'    => 'field.pcre_replace',
            'required' => false,
            'attr'     => ['maxlength' => FieldPCRE::MAX_PCRE],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pcre';
    }
}
