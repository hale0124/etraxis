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

use eTraxis\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Group form.
 */
class GroupForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Group name.
        $builder->add('name', 'text', [
            'label'    => 'group.name',
            'attr'     => ['maxlength' => Group::MAX_NAME],
        ]);

        // Description.
        $builder->add('description', 'text', [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => Group::MAX_DESCRIPTION],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'group';
    }
}
