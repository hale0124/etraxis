<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Form;

use eTraxis\Model\CsvDelimiterStaticCollection;
use eTraxis\Model\EncodingStaticCollection;
use eTraxis\Model\LineEndingStaticCollection;
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
            'choices'  => CsvDelimiterStaticCollection::getTranslatedCollection($this->translator),
        ]);

        // Encoding.
        $builder->add('encoding', 'choice', [
            'label'    => 'encoding',
            'required' => true,
            'choices'  => EncodingStaticCollection::getCollection(),
        ]);

        // Line endings.
        $builder->add('tail', 'choice', [
            'label'    => 'line_endings',
            'required' => true,
            'choices'  => LineEndingStaticCollection::getCollection(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'export';
    }
}
