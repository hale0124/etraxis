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
use eTraxis\Dictionary\TemplatePermission;

/**
 * Template/Group permission.
 *
 * @ORM\Table(name="template_group_permissions")
 * @ORM\Entity
 */
class TemplateGroupPermission
{
    /**
     * @var Template Template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Template", inversedBy="groupPermissions")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $template;

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
     * @var string Permission granted to the group for this template.
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
