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
use eTraxis\Repository\StringValuesRepository;
use eTraxis\SimpleBus\Fields\CreateStringFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateStringFieldCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class StringFieldCommandHandler extends BaseFieldCommandHandler
{
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface        $logger
     * @param   ValidatorInterface     $validator
     * @param   RegistryInterface      $doctrine
     * @param   StringValuesRepository $repository
     */
    public function __construct(
        LoggerInterface        $logger,
        ValidatorInterface     $validator,
        RegistryInterface      $doctrine,
        StringValuesRepository $repository)
    {
        parent::__construct($logger, $validator, $doctrine);

        $this->repository = $repository;
    }

    /**
     * Creates or updates "string" field.
     *
     * @param   CreateStringFieldCommand|UpdateStringFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity
            ->setType(Field::TYPE_STRING)
            ->setParameter1($command->maxLength)
            ->setDefaultValue($this->repository->save($command->default))
            ->setRegexCheck($command->regexCheck)
            ->setRegexSearch($command->regexSearch)
            ->setRegexReplace($command->regexReplace)
        ;

        $this->doctrine->getManager()->persist($entity);
        $this->doctrine->getManager()->flush();
    }
}
