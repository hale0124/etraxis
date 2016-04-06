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

use eTraxis\Entity\Project;
use eTraxis\SimpleBus\Projects\UpdateProjectCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateProjectCommandHandler
{
    protected $validator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   ValidatorInterface $validator
     * @param   RegistryInterface  $doctrine
     */
    public function __construct(ValidatorInterface $validator, RegistryInterface $doctrine)
    {
        $this->validator = $validator;
        $this->doctrine  = $doctrine;
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
        $repository = $this->doctrine->getRepository(Project::class);

        /** @var Project $entity */
        $entity = $repository->find($command->id);

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

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
