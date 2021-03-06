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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Change password form.
 */
class ChangePasswordForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Current password.
        $builder->add('current_password', PasswordType::class, [
            'label'    => 'user.current_password',
            'required' => false,
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // New password.
        $builder->add('new_password', PasswordType::class, [
            'label'    => 'user.new_password',
            'required' => false,
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Confirmation.
        $builder->add('confirmation', PasswordType::class, [
            'label'    => 'user.password_confirmation',
            'required' => false,
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'change_password';
    }
}
