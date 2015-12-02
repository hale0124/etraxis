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

use eTraxis\Collection\Locale;
use eTraxis\Collection\Theme;
use eTraxis\Collection\Timezone;
use eTraxis\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * User form.
 */
class UserForm extends AbstractType
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
        /** @var User $user */
        $user = $builder->getData();

        // User name.
        $builder->add('username', TextType::class, [
            'label'    => 'user.username',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_USERNAME],
        ]);

        // Full name.
        $builder->add('fullname', TextType::class, [
            'label'    => 'user.fullname',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_FULLNAME],
        ]);

        // Email.
        $builder->add('email', EmailType::class, [
            'label'    => 'user.email',
            'disabled' => is_object($user) && $user->isLdap(),
            'attr'     => ['maxlength' => User::MAX_EMAIL],
        ]);

        // Description.
        $builder->add('description', TextType::class, [
            'label'    => 'description',
            'required' => false,
            'attr'     => ['maxlength' => User::MAX_DESCRIPTION],
        ]);

        // Cannot manage passwords of LDAP accounts.
        if (!is_object($user) || !$user->isLdap()) {

            // Password.
            $builder->add('password', PasswordType::class, [
                'label'    => 'user.password',
                'required' => !(is_object($user) && $user->getId()),
                'mapped'   => false,
                'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
            ]);

            // Confirmation.
            $builder->add('confirmation', PasswordType::class, [
                'label'    => 'user.password_confirmation',
                'required' => !(is_object($user) && $user->getId()),
                'mapped'   => false,
                'attr'     => ['maxlength' => BasePasswordEncoder::MAX_PASSWORD_LENGTH],
            ]);
        }

        // Locale.
        $builder->add('locale', ChoiceType::class, [
            'label'             => 'language',
            'required'          => true,
            'choices'           => array_flip(Locale::getTranslatedCollection($this->translator)),
            'choices_as_values' => true,
        ]);

        // Theme.
        $builder->add('theme', ChoiceType::class, [
            'label'             => 'theme',
            'required'          => true,
            'choices'           => array_flip(Theme::getCollection()),
            'choices_as_values' => true,
        ]);

        // Timezone.
        $builder->add('timezone', ChoiceType::class, [
            'label'             => 'timezone',
            'required'          => true,
            'choices'           => array_flip(Timezone::getCollection()),
            'choices_as_values' => true,
        ]);

        // Administrator.
        $builder->add('admin', CheckboxType::class, [
            'label'    => 'role.administrator',
            'required' => false,
        ]);

        // Disabled.
        $builder->add('disabled', CheckboxType::class, [
            'label'    => 'user.disabled',
            'required' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'user';
    }
}
