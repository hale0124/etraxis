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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Reset password form.
 */
class ResetPasswordForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Password.
        $builder->add('password', 'password', [
            'label' => 'user.new_password',
            'attr'  => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Confirmation.
        $builder->add('confirmation', 'password', [
            'label' => 'user.password_confirmation',
            'attr'  => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'reset_password';
    }
}
