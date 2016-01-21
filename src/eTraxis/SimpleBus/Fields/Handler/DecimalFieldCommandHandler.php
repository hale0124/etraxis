<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Fields\Handler;

use eTraxis\Entity\Field;
use eTraxis\Repository\DecimalValuesRepository;
use eTraxis\SimpleBus\Fields\CreateDecimalFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateDecimalFieldCommand;
use eTraxis\SimpleBus\Middleware\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class DecimalFieldCommandHandler extends BaseFieldCommandHandler
{
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface         $logger
     * @param   ValidatorInterface      $validator
     * @param   TranslatorInterface     $translator
     * @param   RegistryInterface       $doctrine
     * @param   DecimalValuesRepository $repository
     */
    public function __construct(
        LoggerInterface         $logger,
        ValidatorInterface      $validator,
        TranslatorInterface     $translator,
        RegistryInterface       $doctrine,
        DecimalValuesRepository $repository)
    {
        parent::__construct($logger, $validator, $translator, $doctrine);

        $this->repository = $repository;
    }

    /**
     * Creates or updates "decimal" field.
     *
     * @param   CreateDecimalFieldCommand|UpdateDecimalFieldCommand $command
     *
     * @throws  ValidationException
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        if (bccomp($command->minValue, $command->maxValue) > 0) {
            $this->logger->error('Minimum valus is greater than maximum one.', [$command->minValue, $command->maxValue]);
            throw new ValidationException([$this->translator->trans('field.min_max_values')]);
        }

        if ($command->default !== null) {
            if (bccomp($command->default, $command->minValue) < 0 || bccomp($command->default, $command->maxValue) > 0) {
                $error = $this->translator->trans('field.default_value', ['%min%' => $command->minValue, '%max%' => $command->maxValue]);
                $this->logger->error($error, [$command->default]);
                throw new ValidationException([$error]);
            }
        }

        $entity
            ->setType(Field::TYPE_DECIMAL)
            ->setParameter1($this->repository->save($command->minValue))
            ->setParameter2($this->repository->save($command->maxValue))
            ->setDefaultValue($this->repository->save($command->default))
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
