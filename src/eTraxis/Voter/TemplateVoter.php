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

use eTraxis\Entity\Template;
use eTraxis\Repository\IssuesRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for "Template" objects.
 */
class TemplateVoter extends Voter
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
            Template::DELETE,
        ];

        if (in_array($attribute, $attributes)) {
            return ($subject instanceof Template);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnoreStart
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Template $subject */
        switch ($attribute) {

            case Template::DELETE:
                return $this->isDeleteGranted($subject);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified template can be deleted.
     *
     * @param   Template $subject Template.
     *
     * @return  bool
     */
    protected function isDeleteGranted($subject)
    {
        // Number of issues created by the template.
        $query = $this->repository->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->leftJoin('i.state', 's')
            ->where('s.templateId = :id')
            ->setParameter('id', $subject->getId())
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one issue has been created by this template.
        return $count == 0;
    }
}
