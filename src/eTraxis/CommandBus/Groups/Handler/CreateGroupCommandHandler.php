<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\CommandBus\Groups\Handler;

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\CommandBus\Groups\CreateGroupCommand;
use eTraxis\Entity\Group;
use eTraxis\Entity\Project;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateGroupCommandHandler
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
     * Creates new group.
     *
     * @param   CreateGroupCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateGroupCommand $command)
    {
        $project = null;

        if ($command->project) {

            /** @var Project $project */
            $project = $this->manager->find(Project::class, $command->project);

            if (!$project) {
                throw new NotFoundHttpException('Unknown project.');
            }
        }

        $entity = new Group($project);

        $entity
            ->setName($command->name)
            ->setDescription($command->description)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
