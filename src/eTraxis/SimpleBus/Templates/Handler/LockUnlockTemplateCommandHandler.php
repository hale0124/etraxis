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

use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\LockTemplateCommand;
use eTraxis\SimpleBus\Templates\UnlockTemplateCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Command handler.
 */
class LockUnlockTemplateCommandHandler
{
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
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
        $repository = $this->doctrine->getRepository(Template::class);

        /** @var Template $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown template.');
        }

        if ($command instanceof LockTemplateCommand) {
            $entity->setLocked(true);
        }
        elseif ($command instanceof UnlockTemplateCommand) {
            $entity->setLocked(false);
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
