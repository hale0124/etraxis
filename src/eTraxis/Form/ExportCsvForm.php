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

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\Encoding;
use eTraxis\Collection\LineEnding;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Export to CSV form.
 */
class ExportCsvForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // File name.
        $builder->add('filename', TextType::class, [
            'label'    => 'file',
            'required' => true,
        ]);

        // Delimiter.
        $builder->add('delimiter', ChoiceType::class, [
            'label'    => 'delimiter',
            'required' => true,
            'choices'  => array_flip(CsvDelimiter::getCollection()),
        ]);

        // Encoding.
        $builder->add('encoding', ChoiceType::class, [
            'label'                     => 'encoding',
            'required'                  => true,
            'choices'                   => array_flip(Encoding::getCollection()),
            'choice_translation_domain' => false,
        ]);

        // Line endings.
        $builder->add('tail', ChoiceType::class, [
            'label'                     => 'line_endings',
            'required'                  => true,
            'choices'                   => array_flip(LineEnding::getCollection()),
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'export';
    }
}
