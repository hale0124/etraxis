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
use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\CreateTemplateCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateTemplateCommandHandler
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
     * Creates new template.
     *
     * @param   CreateTemplateCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateTemplateCommand $command)
    {
        /** @var Project $project */
        $project = $this->manager->find(Project::class, $command->project);

        if (!$project) {
            throw new NotFoundHttpException('Unknown project.');
        }

        $entity = new Template($project);

        $entity
            ->setName($command->name)
            ->setPrefix($command->prefix)
            ->setCriticalAge($command->criticalAge)
            ->setFrozenTime($command->frozenTime)
            ->setDescription($command->description)
            ->setLocked(true)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
