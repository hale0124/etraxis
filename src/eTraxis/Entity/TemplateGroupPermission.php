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
     * @var int Group ID.
     *
     * @ORM\Column(name="group_id", type="integer")
     * @ORM\Id
     */
    private $groupId;

    /**
     * @var int Template ID.
     *
     * @ORM\Column(name="template_id", type="integer")
     * @ORM\Id
     */
    private $templateId;

    /**
     * @var int Permission granted to the group for this template.
     *
     * @ORM\Column(name="perms", type="integer")
     */
    private $permission;

    /**
     * @var Group Group.
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     */
    private $group;

    /**
     * @var Template Template.
     *
     * @ORM\ManyToOne(targetEntity="Template")
     * @ORM\JoinColumn(name="template_id", referencedColumnName="template_id", onDelete="CASCADE")
     */
    private $template;

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
     * @param   int $templateId
     *
     * @return  self
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Standard setter.
     *
     * @param   int $permission
     *
     * @return  self
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Standard getter.
     *
     * @return  int
     */
    public function getPermission()
    {
        return $this->permission;
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

    /**
     * Standard setter.
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
     * Standard getter.
     *
     * @return  Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
