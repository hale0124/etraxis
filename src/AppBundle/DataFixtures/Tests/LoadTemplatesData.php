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


namespace AppBundle\DataFixtures\Tests;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use eTraxis\Model\Template;
use eTraxis\Model\TemplateGroupPermission;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadTemplatesData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $author      = Template::PERMIT_MODIFY_ISSUE | Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE | Template::PERMIT_REMOVE_FILE;
        $responsible = Template::PERMIT_ADD_COMMENT | Template::PERMIT_ADD_FILE;

        $permissions = [
            'group:managers' => [
                Template::PERMIT_VIEW_ISSUE,
                Template::PERMIT_CREATE_ISSUE,
                Template::PERMIT_MODIFY_ISSUE,
                Template::PERMIT_POSTPONE_ISSUE,
                Template::PERMIT_RESUME_ISSUE,
                Template::PERMIT_REASSIGN_ISSUE,
                Template::PERMIT_REOPEN_ISSUE,
                Template::PERMIT_ADD_COMMENT,
                Template::PERMIT_CONFIDENTIAL_COMMENT,
                Template::PERMIT_ADD_FILE,
                Template::PERMIT_REMOVE_FILE,
                Template::PERMIT_SEND_REMINDER,
            ],
            'group:staff' => [
                Template::PERMIT_VIEW_ISSUE,
            ],
            'group:crew' => [
                Template::PERMIT_VIEW_ISSUE,
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
            ->setGuestAccess(true)
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
                ->setTemplateId($template->getId())
                ->setGroupId($group->getId())
                ->setPermission($flags)
                ->setTemplate($template)
                ->setGroup($group)
            ;

            $manager->persist($permission);
        }

        $manager->flush();
    }
}
