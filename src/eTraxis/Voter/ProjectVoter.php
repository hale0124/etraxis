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

use Doctrine\ORM\EntityManagerInterface;
use eTraxis\Entity\Project;
use eTraxis\Entity\Record;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Project" objects.
 */
class ProjectVoter extends Voter
{
    protected $manager;

    /**
     * Dependency Injection constructor.
     *
     * @param   EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
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
     * @codeCoverageIgnore
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
    protected function isDeleteGranted(Project $subject): bool
    {
        // Number of records belong to the project.
        $query = $this->manager->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from(Record::class, 'r')
            ->leftJoin('r.state', 's')
            ->leftJoin('s.template', 't')
            ->where('t.project = :project')
            ->setParameter('project', $subject)
        ;

        $count = (int) $query->getQuery()->getSingleScalarResult();

        // Can't delete if project contains at least one record.
        return $count === 0;
    }
}
