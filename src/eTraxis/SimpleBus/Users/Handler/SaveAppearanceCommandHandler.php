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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\User;
use eTraxis\SimpleBus\Users\SaveAppearanceCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Command handler.
 */
class SaveAppearanceCommandHandler
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
     * Saves appearance settings of specified account.
     *
     * @param   SaveAppearanceCommand $command
     *
     * @throws  NotFoundHttpException
     */
    public function handle(SaveAppearanceCommand $command)
    {
        /** @var User $entity */
        $entity = $this->manager->find(User::class, $command->id);

        if (!$entity) {
            throw new NotFoundHttpException('Unknown user.');
        }

        $entity
            ->setLocale($command->locale)
            ->setTheme($command->theme)
            ->setTimezone($command->timezone)
        ;

        $this->manager->persist($entity);
        $this->manager->flush();
    }
}
