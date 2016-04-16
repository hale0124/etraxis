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
use eTraxis\SimpleBus\Templates\UpdateTemplateCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class UpdateTemplateCommandHandler
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
     * Updates specified template.
     *
     * @param   UpdateTemplateCommand $command
     *
     * @throws  BadRequestHttpException
     * @throws  NotFoundHttpException
     */
    public function handle(UpdateTemplateCommand $command)
    {
        /** @var Template $entity */
        $entity = $this->manager->find(Template::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown template.');
        }

        $entity
            ->setName($command->name)
            ->setPrefix($command->prefix)
            ->setCriticalAge($command->criticalAge)
            ->setFrozenTime($command->frozenTime)
            ->setDescription($command->description)
            ->setGuestAccess($command->guestAccess)
        ;

        $errors = $this->validator->validate($entity);

        if (count($errors)) {
            throw new BadRequestHttpException($errors->get(0)->getMessage());
        }

        $this->manager->persist($entity);
    }
}
