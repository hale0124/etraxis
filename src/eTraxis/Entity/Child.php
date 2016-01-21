<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014-2016 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subissue.
 *
 * @ORM\Table(name="tbl_children")
 * @ORM\Entity
 */
class Child
{
    /**
     * @var int ID of the parent issue.
     *
     * @ORM\Column(name="parent_id", type="integer")
     * @ORM\Id
     */
    private $parentId;

    /**
     * @var int ID of the child issue.
     *
     * @ORM\Column(name="child_id", type="integer")
     * @ORM\Id
     */
    private $childId;

    /**
     * @var int Whether the child is a dependency for the parent.
     *
     * @ORM\Column(name="is_dependency", type="integer")
     */
    private $isDependency;

    /**
     * @var Issue Parent issue.
     *
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="record_id")
     */
    private $parent;

    /**
     * @var Issue Child issue.
     *
     * @ORM\ManyToOne(targetEntity="Issue")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="record_id")
     */
    private $child;

    /**
     * Standard setter.
     *
     * @param   int $parentId
     *
     * @return  self
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Standard setter.
     *
     * @param   int $childId
     *
     * @return  self
     */
    public function setChildId($childId)
    {
        $this->childId = $childId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getChildId()
    {
        return $this->childId;
    }

    /**
     * Standard setter.
     *
     * @param   bool $isDependency
     *
     * @return  self
     */
    public function setDependency($isDependency)
    {
        $this->isDependency = $isDependency ? 1 : 0;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  bool
     */
    public function isDependency()
    {
        return (bool) $this->isDependency;
    }

    /**
     * Standard setter.
     *
     * @param   Issue $parent
     *
     * @return  self
     */
    public function setParent(Issue $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Standard setter.
     *
     * @param   Issue $child
     *
     * @return  self
     */
    public function setChild(Issue $child)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Issue
     */
    public function getChild()
    {
        return $this->child;
    }
}
