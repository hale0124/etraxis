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

use eTraxis\Collection\Locale;
use eTraxis\Collection\Theme;
use eTraxis\Collection\Timezone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Appearance form.
 */
class AppearanceForm extends AbstractType
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
        // Locale.
        $builder->add('locale', ChoiceType::class, [
            'label'    => 'language',
            'required' => true,
            'choices'  => Locale::getTranslatedCollection($this->translator),
        ]);

        // Theme.
        $builder->add('theme', ChoiceType::class, [
            'label'    => 'theme',
            'required' => true,
            'choices'  => Theme::getCollection(),
        ]);

        // Timezone.
        $builder->add('timezone', ChoiceType::class, [
            'label'    => 'timezone',
            'required' => true,
            'choices'  => Timezone::getCollection(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appearance';
    }
}
