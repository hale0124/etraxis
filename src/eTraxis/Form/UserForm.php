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

use eTraxis\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * User form.
 */
class UserForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // User name.
        $builder->add('username', 'text', [
            'label' => 'user.username',
            'attr'  => ['maxlength' => User::MAX_USERNAME],
        ]);

        // Full name.
        $builder->add('fullname', 'text', [
            'label' => 'user.fullname',
            'attr'  => ['maxlength' => User::MAX_FULLNAME],
        ]);

        // Email.
        $builder->add('email', 'email', [
            'label' => 'user.email',
            'attr'  => ['maxlength' => User::MAX_EMAIL],
        ]);

        // Description.
        $builder->add('description', 'text', [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => User::MAX_DESCRIPTION],
        ]);

        // Password.
        $builder->add('password', 'password', [
            'label'    => 'user.password',
            'required' => false,
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Confirmation.
        $builder->add('confirmation', 'password', [
            'label'    => 'user.password_confirmation',
            'required' => false,
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Administrator.
        $builder->add('admin', 'checkbox', [
            'label'    => 'role.administrator',
            'required' => false,
        ]);

        // Disabled.
        $builder->add('disabled', 'checkbox', [
            'label'    => 'user.disabled',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'user';
    }
}
