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
use eTraxis\Model\LocaleStaticCollection;
use eTraxis\Model\ThemeStaticCollection;
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
        /** @var User $user */
        $user = $builder->getData();

        // User name.
        $builder->add('username', 'text', [
            'label'    => 'user.username',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_USERNAME],
        ]);

        // Full name.
        $builder->add('fullname', 'text', [
            'label'    => 'user.fullname',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_FULLNAME],
        ]);

        // Email.
        $builder->add('email', 'email', [
            'label'    => 'user.email',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_EMAIL],
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
            'required' => !(is_object($user) && $user->getId()),
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Confirmation.
        $builder->add('confirmation', 'password', [
            'label'    => 'user.password_confirmation',
            'required' => !(is_object($user) && $user->getId()),
            'mapped'   => false,
            'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
        ]);

        // Locale.
        $builder->add('locale', 'choice', [
            'label'    => 'language',
            'required' => true,
            'choices'  => LocaleStaticCollection::getCollection(),
        ]);

        // Theme.
        $builder->add('theme', 'choice', [
            'label'    => 'theme',
            'required' => true,
            'choices'  => ThemeStaticCollection::getCollection(),
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
