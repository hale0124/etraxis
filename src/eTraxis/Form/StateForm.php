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

use eTraxis\Dictionary\StateResponsible;
use eTraxis\Dictionary\StateType;
use eTraxis\Entity\State;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * State form.
 */
class StateForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var State $state */
        $state = $builder->getData();

        // State name.
        $builder->add('name', TextType::class, [
            'label'    => 'state.name',
            'required' => true,
            'attr'     => ['maxlength' => State::MAX_NAME],
        ]);

        // State abbreviation.
        $builder->add('abbreviation', TextType::class, [
            'label'    => 'state.abbreviation',
            'required' => true,
            'attr'     => ['maxlength' => State::MAX_ABBREVIATION],
        ]);

        // State type.
        if (is_object($state)) {
            // Cannot change type of existing state.
            $builder->add('type', HiddenType::class, [
                'required' => true,
            ]);
        }
        else {
            $builder->add('type', ChoiceType::class, [
                'label'    => 'state.type',
                'required' => true,
                'choices'  => array_flip(StateType::all()),
                'data'     => StateType::INTERIM,
            ]);
        }

        // Responsible.
        if (is_object($state) && $state->getType() === StateType::FINAL) {
            // Cannot change "Responsible" for existing final state.
            $builder->add('responsible', HiddenType::class, [
                'required' => true,
            ]);
        }
        else {
            $builder->add('responsible', ChoiceType::class, [
                'label'    => 'state.responsible',
                'required' => true,
                'choices'  => array_flip(StateResponsible::all()),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'state';
    }
}
