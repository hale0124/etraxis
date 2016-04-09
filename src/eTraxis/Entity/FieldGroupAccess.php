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
 * Field/Group access level.
 *
 * @ORM\Table(name="tbl_field_perms")
 * @ORM\Entity
 */
class FieldGroupAccess
{
    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="field_id", onDelete="CASCADE")
     */
    private $field;

    /**
     * @var Group Group.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var int Access level of the group.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="perms", type="integer")
     */
    private $access;

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Property setter.
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
     * Property getter.
     *
     * @return  int
     */
    public function getAccess()
    {
        return $this->access;
    }
}
