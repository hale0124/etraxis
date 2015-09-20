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

use eTraxis\Collection\Locale;
use eTraxis\Collection\Theme;
use eTraxis\Collection\Timezone;
use Symfony\Component\Form\AbstractType;
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
        $builder->add('locale', 'choice', [
            'label'    => 'language',
            'required' => true,
            'choices'  => Locale::getTranslatedCollection($this->translator),
        ]);

        // Theme.
        $builder->add('theme', 'choice', [
            'label'    => 'theme',
            'required' => true,
            'choices'  => Theme::getCollection(),
        ]);

        // Timezone.
        $builder->add('timezone', 'choice', [
            'label'    => 'timezone',
            'required' => true,
            'choices'  => Timezone::getCollection(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'appearance';
    }
}
