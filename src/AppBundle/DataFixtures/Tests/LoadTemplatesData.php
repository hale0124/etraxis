<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Dictionary\SystemRole;
use eTraxis\Entity\Template;
use eTraxis\Entity\TemplateGroupPermission;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTemplatesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->loadDeliveryTemplate($manager);
        $this->loadFuturamaTemplate($manager);
        $this->loadPhpPsrTemplate($manager);
    }

    /**
     * Loads "Delivery" template for "Planet Express" project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadDeliveryTemplate(ObjectManager $manager)
    {
        $author      = Template::PERMIT_EDIT_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;
        $responsible = Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE;

        $permissions = [
            'group:managers' => [
                Template::PERMIT_VIEW_RECORD,
                Template::PERMIT_CREATE_RECORD,
                Template::PERMIT_EDIT_RECORD,
                Template::PERMIT_POSTPONE_RECORD,
                Template::PERMIT_RESUME_RECORD,
                Template::PERMIT_REASSIGN_RECORD,
                Template::PERMIT_REOPEN_RECORD,
                Template::PERMIT_ADD_COMMENT,
                Template::PERMIT_PRIVATE_COMMENT,
                Template::PERMIT_ADD_FILE,
                Template::PERMIT_REMOVE_FILE,
                Template::PERMIT_SEND_REMINDER,
            ],
            'group:staff' => [
                Template::PERMIT_VIEW_RECORD,
            ],
            'group:crew' => [
                Template::PERMIT_VIEW_RECORD,
                Template::PERMIT_ADD_COMMENT,
            ],
        ];

        $template = new Template();

        /** @noinspection PhpParamsInspection */
        $template
            ->setProject($this->getReference('project:planetexpress'))
            ->setName('Delivery')
            ->setPrefix('PE')
            ->setDescription('Delivery task')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::AUTHOR, $author)
            ->setRolePermissions(SystemRole::RESPONSIBLE, $responsible)
            ->setRolePermissions(SystemRole::REGISTERED, 0)
        ;

        $this->addReference('template:delivery', $template);

        $manager->persist($template);
        $manager->flush();

        foreach ($permissions as $group_ref => $permits) {

            $flags = 0;

            foreach ($permits as $permit) {
                $flags |= $permit;
            }

            $group = $this->getReference($group_ref);

            $permission = new TemplateGroupPermission();

            /** @noinspection PhpParamsInspection */
            $permission
                ->setGroup($group)
                ->setTemplate($template)
                ->setPermission($flags)
            ;

            $manager->persist($permission);
        }

        $manager->flush();
    }

    /**
     * Loads "Futurama" template for "Planet Express" project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaTemplate(ObjectManager $manager)
    {
        $author     = Template::PERMIT_EDIT_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;
        $registered = Template::PERMIT_VIEW_RECORD;

        $template = new Template();

        /** @noinspection PhpParamsInspection */
        $template
            ->setProject($this->getReference('project:planetexpress'))
            ->setName('Futurama')
            ->setPrefix('F')
            ->setDescription('Futurama episode')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::AUTHOR, $author)
            ->setRolePermissions(SystemRole::RESPONSIBLE, 0)
            ->setRolePermissions(SystemRole::REGISTERED, $registered)
        ;

        $this->addReference('template:futurama', $template);

        $manager->persist($template);
        $manager->flush();
    }

    /**
     * Loads "PSR" template for "PHP-FIG" project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadPhpPsrTemplate(ObjectManager $manager)
    {
        $author = Template::PERMIT_EDIT_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;

        $template = new Template();

        /** @noinspection PhpParamsInspection */
        $template
            ->setProject($this->getReference('project:phpfig'))
            ->setName('PSR')
            ->setPrefix('fig')
            ->setDescription('PHP Standard Recommendation')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::AUTHOR, $author)
            ->setRolePermissions(SystemRole::RESPONSIBLE, 0)
            ->setRolePermissions(SystemRole::REGISTERED, 0)
        ;

        $this->addReference('template:phppsr', $template);

        $permission = new TemplateGroupPermission();

        /** @noinspection PhpParamsInspection */
        $permission
            ->setTemplate($template)
            ->setGroup($this->getReference('group:fig:members'))
            ->setPermission(Template::PERMIT_VIEW_RECORD | Template::PERMIT_CREATE_RECORD | Template::PERMIT_ADD_COMMENT)
        ;

        $manager->persist($template);
        $manager->persist($permission);
        $manager->flush();
    }
}
