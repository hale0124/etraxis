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

use eTraxis\Collection\Locale;
use eTraxis\Collection\Theme;
use eTraxis\Collection\Timezone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Appearance form.
 */
class AppearanceForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Locale.
        $builder->add('locale', ChoiceType::class, [
            'label'                     => 'language',
            'required'                  => true,
            'choices'                   => array_flip(Locale::getCollection()),
            'choice_translation_domain' => false,
        ]);

        // Theme.
        $builder->add('theme', ChoiceType::class, [
            'label'                     => 'theme',
            'required'                  => true,
            'choices'                   => array_flip(Theme::getCollection()),
            'choice_translation_domain' => false,
        ]);

        // Timezone.
        $builder->add('timezone', ChoiceType::class, [
            'label'                     => 'timezone',
            'required'                  => true,
            'choices'                   => array_flip(Timezone::getCollection()),
            'choice_translation_domain' => false,
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
