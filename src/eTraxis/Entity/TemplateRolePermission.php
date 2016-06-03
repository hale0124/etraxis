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
     * Property setter.
     *
     * @param   Template $template
     *
     * @return  self
     */
    public function setTemplate(Template $template)
    {
        $this->template = $template;

        return $this;
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
     * Property setter.
     *
     * @param   string $role
     *
     * @return  self
     */
    public function setRole(string $role)
    {
        if (SystemRole::has($role)) {
            $this->role = $role;
        }

        return $this;
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
     * Property setter.
     *
     * @param   string $permission
     *
     * @return  self
     */
    public function setPermission(string $permission)
    {
        if (TemplatePermission::has($permission)) {
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
