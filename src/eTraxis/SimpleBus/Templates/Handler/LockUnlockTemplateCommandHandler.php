<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Templates\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\LockTemplateCommand;
use eTraxis\SimpleBus\Templates\UnlockTemplateCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class LockUnlockTemplateCommandHandler
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Locks specified template.
     *
     * @param   LockTemplateCommand|UnlockTemplateCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle($command)
    {
        /** @var Template $entity */
        $entity = $this->manager->find(Template::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown template.');
        }

        if ($command instanceof LockTemplateCommand) {
            $entity->setLocked(true);
        }
        elseif ($command instanceof UnlockTemplateCommand) {
            $entity->setLocked(false);
        }

        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
