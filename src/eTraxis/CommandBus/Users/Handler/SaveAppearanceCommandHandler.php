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

namespace eTraxis\CommandBus\Users\Handler;

use eTraxis\CommandBus\CommandException;
use eTraxis\CommandBus\Users\SaveAppearanceCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class SaveAppearanceCommandHandler
{
    protected $logger;
    protected $validator;
    protected $translator;
    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface     $logger
     * @param   ValidatorInterface  $validator
     * @param   TranslatorInterface $translator
     * @param   RegistryInterface   $doctrine
     */
    public function __construct(
        LoggerInterface     $logger,
        ValidatorInterface  $validator,
        TranslatorInterface $translator,
        RegistryInterface   $doctrine)
    {
        $this->logger     = $logger;
        $this->validator  = $validator;
        $this->translator = $translator;
        $this->doctrine   = $doctrine;
    }

    /**
     * Saves appearance settings of specified account.
     *
     * @param   SaveAppearanceCommand $command
     *
     * @throws  CommandException
     * @throws  NotFoundHttpException
     */
    public function handle(SaveAppearanceCommand $command)
    {
        $repository = $this->doctrine->getRepository('eTraxis:User');

        /** @var \eTraxis\Entity\User $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
            $this->logger->error('Unknown user.', [$command->id]);
            throw new NotFoundHttpException('Unknown user.');
        }

        $entity
            ->setLocale($command->locale)
            ->setTheme($command->theme)
            ->setTimezone($command->timezone)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
