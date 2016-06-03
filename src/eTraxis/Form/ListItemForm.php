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

use eTraxis\Entity\ListItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * List item form.
 */
class ListItemForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ListItem $data */
        $data = $builder->getData();

        // Cannot change value of existing list item.
        if (!is_object($data)) {
            // Item's value.
            $builder->add('value', TextType::class, [
                'label'    => 'listitem.value',
                'required' => true,
                'attr'     => ['maxlength' => strlen(PHP_INT_MAX)],
            ]);
        }

        // Item's text.
        $builder->add('text', TextType::class, [
            'label'    => 'listitem.text',
            'required' => true,
            'attr'     => ['maxlength' => ListItem::MAX_TEXT],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'listitem';
    }
}
