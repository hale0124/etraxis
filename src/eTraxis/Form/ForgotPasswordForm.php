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

/**
 * Forgot password form.
 */
class ForgotPasswordForm extends AbstractType
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
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'forgot_password';
    }
}
