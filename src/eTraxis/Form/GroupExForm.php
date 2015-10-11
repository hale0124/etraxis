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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Group form extended with project selector.
 */
class GroupExForm extends GroupForm
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
        parent::buildForm($builder, $options);

        // Project.
        $builder->add('project', 'entity', [
            'label'       => 'project',
            'required'    => false,
            'class'       => 'eTraxis:Project',
            'property'    => 'name',
            'placeholder' => $this->translator->trans('group.global'),
        ]);
    }
}
