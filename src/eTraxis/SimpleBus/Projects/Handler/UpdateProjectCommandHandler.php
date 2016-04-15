<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Projects\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Project;
use eTraxis\SimpleBus\Projects\UpdateProjectCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateProjectCommandHandler
{
    protected $validator;
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface     $validator
     * @param   EntityManagerInterface $manager
     */
    public function __construct(ValidatorInterface $validator, EntityManagerInterface $manager)
    {
        $this->validator = $validator;
        $this->manager   = $manager;
    }

    /**
     * Updates specified project.
     *
     * @param   UpdateProjectCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateProjectCommand $command)
    {
        /** @var Project $entity */
        $entity = $this->manager->find(Project::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown project.');
        }

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
            ->setSuspended($command->suspended)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
