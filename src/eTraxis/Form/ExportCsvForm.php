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

use eTraxis\Collection\CsvDelimiter;
use eTraxis\Collection\Encoding;
use eTraxis\Collection\LineEnding;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Export to CSV form.
 */
class ExportCsvForm extends AbstractType
{
    protected $translator;

    /**
     * Dependency Injection constructor.
     *
     * @param   TranslatorInterface $translator
     */
    public function __construct($translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // File name.
        $builder->add('filename', 'text', [
            'label' => 'file',
        ]);

        // Delimiter.
        $builder->add('delimiter', 'choice', [
            'label'    => 'delimiter',
            'required' => true,
            'choices'  => CsvDelimiter::getTranslatedCollection($this->translator),
        ]);

        // Encoding.
        $builder->add('encoding', 'choice', [
            'label'    => 'encoding',
            'required' => true,
            'choices'  => Encoding::getCollection(),
        ]);

        // Line endings.
        $builder->add('tail', 'choice', [
            'label'    => 'line_endings',
            'required' => true,
            'choices'  => LineEnding::getCollection(),
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
