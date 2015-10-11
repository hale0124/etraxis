<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2015 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\Project;
use eTraxis\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter for "Project" objects.
 */
class ProjectVoter extends AbstractVoter
{
    const DELETE = 'project.delete';

    protected $doctrine;

    /**
     * Dependency Injection constructor.
     *
     * @param   RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return ['eTraxis\Entity\Project'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return [
            self::DELETE,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var Project $object */
        switch ($attribute) {

            case self::DELETE:
                return $this->isDeleteGranted($object, $user);

            default:
                return false;
        }
    }

    /**
     * Checks whether current user can delete specified project.
     *
     * @param   Project $object Project.
     * @param   User    $user   Current user.
     *
     * @return  bool
     */
    protected function isDeleteGranted($object, $user = null)
    {
        /** @var User $user */
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$user->isAdmin()) {
            return false;
        }

        /** @var \Doctrine\ORM\EntityRepository $repository */
        $repository = $this->doctrine->getRepository('eTraxis:Issue');

        // Number of issues belong to the project.
        $query = $repository->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->leftJoin('i.state', 's')
            ->leftJoin('s.template', 't')
            ->where('t.projectId = :id')
            ->setParameter('id', $object->getId())
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if project contains at least one issue.
        return $count == 0;
    }
}
