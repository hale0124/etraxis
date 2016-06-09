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
     * Constructor.
     *
     * @param   Field  $field
     * @param   Group  $group
     * @param   string $permission
     */
    public function __construct(Field $field, Group $group, string $permission)
    {
        $this->field = $field;
        $this->group = $group;

        if (FieldPermission::has($permission)) {
            $this->permission = $permission;
        }
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
     * Property getter.
     *
     * @return  Group
     */
    public function getGroup()
    {
        return $this->group;
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
