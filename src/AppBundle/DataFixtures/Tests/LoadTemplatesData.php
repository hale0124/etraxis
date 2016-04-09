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
            ->setName('Delivery')
            ->setPrefix('PE')
            ->setDescription('Delivery task')
            ->setLocked(false)
            ->setGuestAccess(false)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions($author)
            ->setResponsiblePermissions($responsible)
            ->setProject($this->getReference('project:planetexpress'))
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
                ->setTemplate($template)
                ->setGroup($group)
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
        $registered = Template::PERMIT_VIEW_RECORD;
        $author     = Template::PERMIT_EDIT_RECORD | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;

        $template = new Template();

        /** @noinspection PhpParamsInspection */
        $template
            ->setName('Futurama')
            ->setPrefix('F')
            ->setDescription('Futurama episode')
            ->setLocked(false)
            ->setGuestAccess(true)
            ->setRegisteredPermissions($registered)
            ->setAuthorPermissions($author)
            ->setResponsiblePermissions(0)
            ->setProject($this->getReference('project:planetexpress'))
        ;

        $this->addReference('template:futurama', $template);

        $manager->persist($template);
        $manager->flush();
    }
}
