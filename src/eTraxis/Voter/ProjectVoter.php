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
use eTraxis\Repository\IssuesRepository;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Voter for "Project" objects.
 */
class ProjectVoter extends AbstractVoter
{
    const DELETE = 'project.delete';

    protected $repository;

    /**
     * Dependency Injection constructor.
     *
     * @param   IssuesRepository $repository
     */
    public function __construct(IssuesRepository $repository)
    {
        $this->repository = $repository;
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
                return $this->isDeleteGranted($object);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified project can be deleted.
     *
     * @param   Project $object Project.
     *
     * @return  bool
     */
    protected function isDeleteGranted($object)
    {
        // Number of issues belong to the project.
        $query = $this->repository->createQueryBuilder('i')
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
