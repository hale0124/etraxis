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
use eTraxis\Dictionary\FieldPermission;

/**
 * Field/Group permission.
 *
 * @ORM\Table(name="field_group_permissions")
 * @ORM\Entity
 */
class FieldGroupPermission
{
    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="groupPermissions")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $field;

    /**
     * @var Group Group.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var string Permission of the group.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string", length=20)
     */
    private $permission;

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
     * @param   string $permission
     *
     * @return  self
     */
    public function setPermission(string $permission)
    {
        if (FieldPermission::has($permission)) {
            $this->permission = $permission;
        }

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  string
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
