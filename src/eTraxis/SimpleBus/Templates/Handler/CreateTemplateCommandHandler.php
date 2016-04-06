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

use eTraxis\Entity\Project;
use eTraxis\Entity\Template;
use eTraxis\SimpleBus\Templates\CreateTemplateCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class CreateTemplateCommandHandler
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
     * Creates new template.
     *
     * @param   CreateTemplateCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(CreateTemplateCommand $command)
    {
        $repository = $this->doctrine->getRepository(Project::class);

        /** @var Project $project */
        $project = $repository->find($command->project);

        if (!$project) {
            throw new NotFoundHttpException('Unknown project.');
        }

        $entity = new Template();

        $entity
            ->setProject($project)
            ->setName($command->name)
            ->setPrefix($command->prefix)
            ->setDescription($command->description)
            ->setCriticalAge($command->criticalAge)
            ->setFrozenTime($command->frozenTime)
            ->setGuestAccess($command->guestAccess)
            ->setLocked(true)
            ->setRegisteredPermissions(0)
            ->setAuthorPermissions(Template::PERMIT_VIEW_RECORD)
            ->setResponsiblePermissions(Template::PERMIT_VIEW_RECORD)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
