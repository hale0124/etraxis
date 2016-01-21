<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Voter;

use eTraxis\Entity\Project;
use eTraxis\Repository\IssuesRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Project" objects.
 */
class ProjectVoter extends Voter
{
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
    protected function supports($attribute, $subject)
    {
        $attributes = [
            Project::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
           return $subject instanceof Project;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Project $subject */
        switch ($attribute) {

            case Project::DELETE:
                return $this->isDeleteGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified project can be deleted.
     *
     * @param   Project $subject Project.
     *
     * @return  bool
     */
    protected function isDeleteGranted($subject)
    {
        // Number of issues belong to the project.
        $query = $this->repository->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->leftJoin('i.state', 's')
            ->leftJoin('s.template', 't')
            ->where('t.projectId = :id')
            ->setParameter('id', $subject->getId())
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if project contains at least one issue.
        return $count == 0;
    }
}
