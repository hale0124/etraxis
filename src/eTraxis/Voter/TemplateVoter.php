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
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Voter for "Template" objects.
 */
class TemplateVoter extends AbstractVoter
{
    const DELETE = 'template.delete';

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
        return ['eTraxis\Entity\Template'];
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
        /** @var Template $object */
        switch ($attribute) {

            case self::DELETE:
                return $this->isDeleteGranted($object);

            default:
                return false;
        }
    }

    /**
     * Checks whether specified template can be deleted.
     *
     * @param   Template $object Template.
     *
     * @return  bool
     */
    protected function isDeleteGranted($object)
    {
        // Number of issues created by the template.
        $query = $this->repository->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->leftJoin('i.state', 's')
            ->where('s.templateId = :id')
            ->setParameter('id', $object->getId())
        ;

        $count = $query->getQuery()->getSingleScalarResult();

        // Can't delete if at least one issue has been created by this template.
        return $count == 0;
    }
}
