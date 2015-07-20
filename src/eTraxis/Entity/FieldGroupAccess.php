<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2014 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis.  If not, see <http://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace eTraxis\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Field/Group access level.
 *
 * @ORM\Table(name="tbl_field_perms")
 * @ORM\Entity
 */
class FieldGroupAccess
{
    /**
     * @var int Field ID.
     *
     * @ORM\Column(name="field_id", type="integer")
     * @ORM\Id
     */
    private $fieldId;

    /**
     * @var int Group ID.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     */
    private $groupId;

    /**
     * @var int Access level of the group.
     *
     * @ORM\Column(name="perms", type="integer")
     * @ORM\Id
     */
    private $access;

    /**
     * @var Field Field.
     *
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id")
     */
    private $field;

    /**
     * @var Group Group.
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
     */
    private $group;

    /**
     * Standard setter.
     *
     * @param   int $fieldId
     *
     * @return  self
     */
    public function setFieldId($fieldId)
    {
        $this->fieldId = $fieldId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * Standard setter.
     *
     * @param   int $groupId
     *
     * @return  self
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Standard setter.
     *
     * @param   int $access
     *
     * @return  self
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Standard setter.
     *
     * @param   Field $field
     *
     * @return  self
     */
    public function setField(Field $field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Standard setter.
     *
     * @param   Group $group
     *
     * @return  self
     */
    public function setGroup(Group $group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }
}
