<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\SimpleBus\Users\Handler;

use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\SaveAppearanceCommand;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class SaveAppearanceCommandHandler
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
     * Saves appearance settings of specified account.
     *
     * @param   SaveAppearanceCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SaveAppearanceCommand $command)
    {
        $repository = $this->doctrine->getRepository(User::class);

        /** @var User $entity */
        $entity = $repository->find($command->id);

        if (!$entity) {
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
