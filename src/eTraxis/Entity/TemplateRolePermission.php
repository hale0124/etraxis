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
use eTraxis\Dictionary\SystemRole;
use eTraxis\Dictionary\TemplatePermission;

/**
 * Template/Role permission.
 *
 * @ORM\Table(name="template_role_permissions")
 * @ORM\Entity
 */
class TemplateRolePermission
{
    /**
     * @var Template Template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="rolePermissions")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var string Role.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="role", type="string", length=20)
     */
    private $role;

    /**
     * @var string Permission granted to the role for this template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="permission", type="string", length=20)
     */
    private $permission;

    /**
     * Constructor.
     *
     * @param   Template $template
     * @param   string   $role
     * @param   string   $permission
     */
    public function __construct(Template $template, string $role, string $permission)
    {
        $this->template = $template;

        if (SystemRole::has($role)) {
            $this->role = $role;
        }

        if (TemplatePermission::has($permission)) {
            $this->permission = $permission;
        }
    }

    /**
     * Property getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->template;
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
