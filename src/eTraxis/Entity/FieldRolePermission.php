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
use eTraxis\Dictionary\SystemRole;

/**
 * Field/Role permission.
 *
 * @ORM\Table(name="field_role_permissions")
 * @ORM\Entity
 */
class FieldRolePermission
{
    /**
     * @var Field Field.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="field_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $field;

    /**
     * @var string Role.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;

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
     * @param   string $role
     * @param   string $permission
     */
    public function __construct(Field $field, string $role, string $permission)
    {
        $this->field = $field;

        if (SystemRole::has($role)) {
            $this->role = $role;
        }

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
     * @return  string
     */
    public function getRole()
    {
        return $this->role;
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
