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
use eTraxis\Repository\TextValuesRepository;
use eTraxis\SimpleBus\Fields\CreateTextFieldCommand;
use eTraxis\SimpleBus\Fields\UpdateTextFieldCommand;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class TextFieldCommandHandler extends BaseFieldCommandHandler
{
    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   LoggerInterface      $logger
     * @param   ValidatorInterface   $validator
     * @param   RegistryInterface    $doctrine
     * @param   TextValuesRepository $repository
     */
    public function __construct(
        LoggerInterface      $logger,
        ValidatorInterface   $validator,
        RegistryInterface    $doctrine,
        TextValuesRepository $repository)
    {
        parent::__construct($logger, $validator, $doctrine);

        $this->repository = $repository;
    }

    /**
     * Creates or updates "text" field.
     *
     * @param   CreateTextFieldCommand|UpdateTextFieldCommand $command
     */
    public function handle($command)
    {
        $entity = $this->getEntity($command);

        $entity
            ->setType(Field::TYPE_TEXT)
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
