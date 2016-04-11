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

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Group form extended with project selector.
 */
class GroupExForm extends GroupForm
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        /** @var \eTraxis\Entity\Group|array $data */
        $data = $builder->getData();

        // Global group.
        $builder->add('project', CheckboxType::class, [
            'label'    => 'group.local',
            'value'    => is_object($data) ? $data->getProject()->getId() : $data['id'],
            'required' => false,
            'attr'     => [
                'checked' => true,
            ],
        ]);
    }
}
