<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Groups\Handler;

use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use eTraxis\SimpleBus\Groups\CreateGroupCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateGroupCommandHandler
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
     * Creates new group.
     *
     * @param   CreateGroupCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateGroupCommand $command)
    {
        $entity = new Group();

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
        ;

        if ($command->project) {

            $repository = $this->doctrine->getRepository(Project::class);

            /** @var Project $project */
            $project = $repository->find($command->project);

            if (!$project) {
                throw new NotFoundHttpException('Unknown project.');
            }

            $entity->setProject($project);
        }

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
