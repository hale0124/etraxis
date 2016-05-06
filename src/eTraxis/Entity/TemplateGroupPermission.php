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
 * Template/Group permission.
 *
 * @ORM\Table(name="tbl_group_perms")
 * @ORM\Entity
 */
class TemplateGroupPermission
{
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
     * @var Template Template.
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="template_id", onDelete="CASCADE")
     */
    private $template;

    /**
     * @var int Permission granted to the group for this template.
     *
     * @ORM\Column(name="perms", type="integer")
     */
    private $permission;

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
     * @param   int $permission
     *
     * @return  self
     */
    public function setPermission(int $permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Property getter.
     *
     * @return  int
     */
    public function getPermission()
    {
        return $this->permission;
    }
}
