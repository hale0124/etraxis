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
use eTraxis\Dictionary\TemplatePermission;
use eTraxis\Entity\Template;
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
        $author = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::EDIT_RECORDS,
            TemplatePermission::POSTPONE_RECORDS,
            TemplatePermission::RESUME_RECORDS,
            TemplatePermission::REASSIGN_RECORDS,
            TemplatePermission::REOPEN_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
            TemplatePermission::ATTACH_SUBRECORDS,
            TemplatePermission::DETACH_SUBRECORDS,
            TemplatePermission::SEND_REMINDERS,
            TemplatePermission::DELETE_RECORDS,
        ];

        $responsible = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::EDIT_RECORDS,
            TemplatePermission::POSTPONE_RECORDS,
            TemplatePermission::RESUME_RECORDS,
            TemplatePermission::REASSIGN_RECORDS,
            TemplatePermission::REOPEN_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
            TemplatePermission::ATTACH_SUBRECORDS,
            TemplatePermission::DETACH_SUBRECORDS,
            TemplatePermission::SEND_REMINDERS,
            TemplatePermission::DELETE_RECORDS,
        ];

        $permissions = [
            'group:managers' => [
                TemplatePermission::VIEW_RECORDS,
                TemplatePermission::CREATE_RECORDS,
                TemplatePermission::EDIT_RECORDS,
                TemplatePermission::POSTPONE_RECORDS,
                TemplatePermission::RESUME_RECORDS,
                TemplatePermission::REASSIGN_RECORDS,
                TemplatePermission::REOPEN_RECORDS,
                TemplatePermission::ADD_COMMENTS,
                TemplatePermission::PRIVATE_COMMENTS,
                TemplatePermission::ATTACH_FILES,
                TemplatePermission::DELETE_FILES,
                TemplatePermission::ATTACH_SUBRECORDS,
                TemplatePermission::DETACH_SUBRECORDS,
                TemplatePermission::SEND_REMINDERS,
                TemplatePermission::DELETE_RECORDS,
            ],
            'group:crew' => [
                TemplatePermission::VIEW_RECORDS,
                TemplatePermission::ADD_COMMENTS,
                TemplatePermission::PRIVATE_COMMENTS,
            ],
        ];

        /** @noinspection PhpParamsInspection */
        $template = new Template($this->getReference('project:planetexpress'));

        $template
            ->setName('Delivery')
            ->setPrefix('PE')
            ->setFrozenTime(7)
            ->setDescription('Delivery task')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::AUTHOR, $author)
            ->setRolePermissions(SystemRole::RESPONSIBLE, $responsible)
        ;

        $this->addReference('template:delivery', $template);

        foreach ($permissions as $group_ref => $group_permissions) {
            /** @var \eTraxis\Entity\Group $group */
            $group = $this->getReference($group_ref);
            $template->setGroupPermissions($group, $group_permissions);
        }

        $manager->persist($template);
        $manager->flush();
    }

    /**
     * Loads "Futurama" template for "Planet Express" project.
     *
     * @param   ObjectManager $manager
     */
    protected function loadFuturamaTemplate(ObjectManager $manager)
    {
        $anyone = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::EDIT_RECORDS,
            TemplatePermission::POSTPONE_RECORDS,
            TemplatePermission::RESUME_RECORDS,
            TemplatePermission::REASSIGN_RECORDS,
            TemplatePermission::REOPEN_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
            TemplatePermission::ATTACH_SUBRECORDS,
            TemplatePermission::DETACH_SUBRECORDS,
            TemplatePermission::SEND_REMINDERS,
            TemplatePermission::DELETE_RECORDS,
        ];

        /** @noinspection PhpParamsInspection */
        $template = new Template($this->getReference('project:planetexpress'));

        $template
            ->setName('Futurama')
            ->setPrefix('F')
            ->setDescription('Futurama episode')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::ANYONE, $anyone)
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
        $author = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::EDIT_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::ATTACH_FILES,
            TemplatePermission::DELETE_FILES,
        ];

        $responsible = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::ADD_COMMENTS,
            TemplatePermission::PRIVATE_COMMENTS,
            TemplatePermission::ATTACH_FILES,
        ];

        $members = [
            TemplatePermission::VIEW_RECORDS,
            TemplatePermission::CREATE_RECORDS,
            TemplatePermission::ADD_COMMENTS,
        ];

        /** @var \eTraxis\Entity\Group $group */
        $group = $this->getReference('group:fig:members');

        /** @noinspection PhpParamsInspection */
        $template = new Template($this->getReference('project:phpfig'));

        $template
            ->setName('PSR')
            ->setPrefix('fig')
            ->setDescription('PHP Standard Recommendation')
            ->setLocked(false)
            ->setRolePermissions(SystemRole::AUTHOR, $author)
            ->setRolePermissions(SystemRole::RESPONSIBLE, $responsible)
            ->setGroupPermissions($group, $members)
        ;

        $this->addReference('template:phppsr', $template);

        $manager->persist($template);
        $manager->flush();
    }
}
